<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\ContactInquiry;
use App\Models\GeneralSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactInquiryAdminMail;
use App\Mail\ContactInquiryUserMail;

class ContactController extends Controller
{
    /**
     * Retrieve CMS-driven contact page content plus general settings.
     */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'contact');

        $page = CmsPage::query()
            ->where('page_slug', $slug)
            ->with([
                'sections' => fn($q) => $q->orderBy('section_sort_order'),
                'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
            ])
            ->first();

        if ($page === null && $slug !== 'contact') {
            $page = CmsPage::query()
                ->where('page_slug', 'contact')
                ->with([
                    'sections' => fn($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
                ])
                ->first();
        }

        if ($page === null) {
            $page = CmsPage::query()
                ->where('page_slug', 'contact-us')
                ->with([
                    'sections' => fn($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
                ])
                ->first();
        }

        $sections = $page ? $this->formatSections($page->sections) : [];

        $settings = GeneralSetting::all()
            ->mapWithKeys(fn($setting) => [$setting->key => $setting->value])
            ->toArray();

        return response()->json([
            'page' => [
                'id' => $page?->id,
                'title' => $page?->page_title,
                'slug' => $page?->page_slug ?? $slug,
                'meta' => [
                    'title' => $page?->page_meta_title,
                    'keywords' => $page?->page_meta_keyword,
                    'description' => $page?->page_meta_description,
                ],
                'sections' => $sections,
            ],
            'settings' => $settings,
        ]);
    }

    /**
     * Store a contact inquiry submitted from the storefront.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'subject' => ['nullable', 'string', 'max:255'],
        ]);

        $inquiry = ContactInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company' => $validated['company'] ?? '',
            'phone' => $validated['phone'] ?? '',
            'service' => $validated['service'] ?? '',
            'message' => $validated['message'],
            'subject' => $validated['subject'] ?? 'Website Inquiry',
            'is_read' => 0,
        ]);

        // Send notifications
        $adminEmail = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        try {
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ContactInquiryAdminMail($inquiry));
            }
            Mail::to($inquiry->email)->send(new ContactInquiryUserMail($inquiry));
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Inquiry saved, but emails could not be sent. Please try again later.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for reaching out. Our team will get back to you shortly.',
            'data' => $inquiry,
        ], 201);
    }

    protected function formatSections(Collection $sections): array
    {
        return $sections
            ->map(static function ($section) {
                $base = [
                    'id' => $section->id,
                    'name' => $section->section_name,
                    'type' => $section->section_type,
                    'sort_order' => $section->section_sort_order,
                ];

                $fields = $section->fields->sortBy('id');

                if ($section->section_type === 'repeater') {
                    $base['items'] = $fields
                        ->groupBy(static fn($field) => $field->field_group ?? 'default')
                        ->sortKeys()
                        ->map(static function (Collection $fieldGroup) {
                            return $fieldGroup
                                ->sortBy('id')
                                ->mapWithKeys(static function ($field) {
                                    $payload = [
                                        'type' => $field->field_type,
                                        'value' => $field->field_value,
                                    ];

                                    if ($field->field_type === 'image' && $field->field_value) {
                                        $payload['url'] = self::resolveImageUrl($field->field_value);
                                    }

                                    return [
                                        $field->field_name => $payload,
                                    ];
                                })
                                ->all();
                        })
                        ->values()
                        ->all();
                } else {
                    $base['fields'] = $fields
                        ->mapWithKeys(static function ($field) {
                            $payload = [
                                'type' => $field->field_type,
                                'value' => $field->field_value,
                            ];

                            if ($field->field_type === 'image' && $field->field_value) {
                                $payload['url'] = self::resolveImageUrl($field->field_value);
                            }

                            return [
                                $field->field_name => $payload,
                            ];
                        })
                        ->all();
                }

                return $base;
            })
            ->values()
            ->all();
    }

    protected static function resolveImageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (preg_match('/^https?:\\/\\//', $path)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return route('media.asset', ['path' => ltrim($path, '/')]);
        }

        return $path;
    }
}
