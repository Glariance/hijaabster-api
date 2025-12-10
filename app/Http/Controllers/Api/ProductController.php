<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Product landing page payload: CMS content + products + categories.
     */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'products');

        // Try the requested slug, then fall back to "shop" if not found.
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

        if ($page === null && $slug !== 'shop') {
            $page = CmsPage::query()
                ->where('page_slug', 'shop')
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
        }

        $sections = $page ? $this->formatSections($page->sections) : [];

        $categories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'slug']);

        $products = ProductResource::collection(
            $this->buildQuery($request)->get()
        );

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
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    /**
     * Retrieve products with optional filtering.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $this->buildQuery($request)->get();

        return ProductResource::collection($products);
    }

    /**
     * Retrieve featured products only.
     */
    public function featured(Request $request): AnonymousResourceCollection
    {
        $request->merge(['featured' => true]);

        $products = $this->buildQuery($request)->get();

        return ProductResource::collection($products);
    }

    /**
     * Base query shared across product endpoints.
     */
    protected function buildQuery(Request $request): Builder
    {
        $query = Product::query()
            ->with(['category', 'mediaFeatured'])
            ->when(! $request->boolean('include_inactive'), function (Builder $builder) {
                $builder->where('status', 1);
            })
            ->when($request->boolean('featured'), function (Builder $builder) {
                $builder->where('featured', 1);
            })
            ->when($request->boolean('new'), function (Builder $builder) {
                $builder->where('new', 1);
            })
            ->when($request->boolean('top'), function (Builder $builder) {
                $builder->where('top', 1);
            })
            ->when($request->filled('category'), function (Builder $builder) use ($request) {
                $category = $request->input('category');

                if (is_numeric($category)) {
                    $builder->where('category_id', $category);
                } else {
                    $builder->whereHas('category', function (Builder $inner) use ($category) {
                        $inner->where('slug', $category);
                    });
                }
            })
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $search = $request->input('search');

                $builder->where(function (Builder $inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('featured')
            ->orderBy('name');

        return $query;
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

        if (preg_match('/^https?:\\/\\//', $path)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return route('media.asset', ['path' => ltrim($path, '/')]);
        }

        return $path;
    }
}
