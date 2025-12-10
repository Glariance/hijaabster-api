<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PrivacyController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'privacy');

        $page = CmsPage::query()
            ->where('page_slug', $slug)
            ->with([
                'sections' => fn($q) => $q->orderBy('section_sort_order'),
                'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
            ])
            ->first();

        if ($page === null && $slug !== 'privacy-policy') {
            $page = CmsPage::query()
                ->where('page_slug', 'privacy-policy')
                ->with([
                    'sections' => fn($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
                ])
                ->first();
        }

        if ($page === null) {
            $page = CmsPage::query()
                ->where('page_slug', 'privacy')
                ->with([
                    'sections' => fn($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
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
