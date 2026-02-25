<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    /**
     * Fetch CMS page data for the checkout page (banner, sections, etc.).
     */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'checkout');

        $page = CmsPage::query()
            ->where('page_slug', $slug)
            ->with([
                'sections' => fn ($q) => $q->orderBy('section_sort_order'),
                'sections.fields' => fn ($q) => $q->orderBy('field_group')->orderBy('id'),
            ])
            ->first();

        if ($page === null && $slug !== 'checkout') {
            $page = CmsPage::query()
                ->where('page_slug', 'checkout')
                ->with([
                    'sections' => fn ($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn ($q) => $q->orderBy('field_group')->orderBy('id'),
                ])
                ->first();
        }

        $sections = $page ? $this->formatSections($page->sections) : [];

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
        ]);
    }

    /**
     * Get checkout config: shipping options, payment highlights, etc.
     */
    public function index(Request $request): JsonResponse
    {
        $shippingOptions = [
            [
                'id' => 'express',
                'title' => 'Complimentary express',
                'description' => 'Priority handling with carbon-neutral courier partners.',
                'eta' => 'Arrives in 2-3 business days',
                'price' => 0,
            ],
            [
                'id' => 'white-glove',
                'title' => 'White glove delivery',
                'description' => 'Scheduled delivery with on-site fit guidance in select cities.',
                'eta' => 'We will reach out within 24 hours to coordinate',
                'price' => 45,
            ],
        ];

        $paymentHighlights = [
            ['label' => 'Secure checkout', 'detail' => 'Payments are encrypted and processed via PCI Level 1 providers.'],
            ['label' => 'Flexible payments', 'detail' => 'Use credit, debit, or split payments with our financing partners.'],
            ['label' => 'Loyalty perks', 'detail' => 'Members earn double points on silk silhouettes through August.'],
        ];

        return response()->json([
            'shippingOptions' => $shippingOptions,
            'paymentHighlights' => $paymentHighlights,
        ]);
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
                        ->groupBy(static fn ($field) => $field->field_group ?? 'default')
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

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return route('media.asset', ['path' => ltrim($path, '/')]);
        }

        return $path;
    }
}
