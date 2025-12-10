<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    /**
     * Return the CMS powered content for the about page.
     */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'about-us');

        $page = CmsPage::query()
            ->where('page_slug', $slug)
            ->with([
                'sections' => function ($query) {
                    $query->orderBy('section_sort_order');
                },
                'sections.fields' => function ($query) {
                    $query->orderBy('field_group')
                        ->orderBy('id');
                },
            ])
            ->first();

        if ($page === null) {
            return response()->json([
                'message' => 'Page not found.',
            ], 404);
        }

        $sections = $page->sections
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

        return response()->json([
            'page' => [
                'id' => $page->id,
                'title' => $page->page_title,
                'slug' => $page->page_slug,
                'meta' => [
                    'title' => $page->page_meta_title,
                    'keywords' => $page->page_meta_keyword,
                    'description' => $page->page_meta_description,
                ],
                'sections' => $sections,
            ],
        ]);
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
