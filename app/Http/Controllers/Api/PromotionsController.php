<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PromotionsController extends Controller
{
    /**
     * Return the CMS powered content for the promotions page.
     */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'promotions');

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

        // If already a full URL, return as is
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        // Remove leading slash and 'storage/' if already present
        $cleanPath = ltrim($path, '/');
        if (strpos($cleanPath, 'storage/') === 0) {
            $cleanPath = substr($cleanPath, 8); // Remove 'storage/' prefix
        }

        // Check if file exists in public/storage (created by storage:link)
        $publicPath = public_path('storage/' . $cleanPath);
        if (file_exists($publicPath) && is_file($publicPath)) {
            // Use url() helper to generate full absolute URL
            $fullUrl = url('storage/' . $cleanPath);
            // Ensure it's an absolute URL (not relative)
            if (!preg_match('/^https?:\/\//', $fullUrl)) {
                // If url() returns relative, prepend APP_URL
                $appUrl = config('app.url', 'http://localhost:8000');
                $fullUrl = rtrim($appUrl, '/') . '/' . ltrim($fullUrl, '/');
            }
            return $fullUrl;
        }

        // Fallback: check in storage/app/public
        if (Storage::disk('public')->exists($cleanPath)) {
            $fullUrl = url('storage/' . $cleanPath);
            // Ensure it's an absolute URL (not relative)
            if (!preg_match('/^https?:\/\//', $fullUrl)) {
                // If url() returns relative, prepend APP_URL
                $appUrl = config('app.url', 'http://localhost:8000');
                $fullUrl = rtrim($appUrl, '/') . '/' . ltrim($fullUrl, '/');
            }
            return $fullUrl;
        }

        // Last resort: try to construct URL anyway
        $fullUrl = url('storage/' . $cleanPath);
        if (!preg_match('/^https?:\/\//', $fullUrl)) {
            $appUrl = config('app.url', 'http://localhost:8000');
            $fullUrl = rtrim($appUrl, '/') . '/' . ltrim($fullUrl, '/');
        }
        return $fullUrl;
    }
}

