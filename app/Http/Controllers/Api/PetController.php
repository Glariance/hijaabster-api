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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    /**
    * Pet landing payload: CMS content + categories + products (pet-only).
    */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'pets');

        $page = CmsPage::query()
            ->where('page_slug', $slug)
            ->with([
                'sections' => fn($q) => $q->orderBy('section_sort_order'),
                'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
            ])
            ->first();

        if ($page === null && $slug !== 'pets') {
            $page = CmsPage::query()
                ->where('page_slug', 'pets')
                ->with([
                    'sections' => fn($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn($q) => $q->orderBy('field_group')->orderBy('id'),
                ])
                ->first();
        }

        $sections = $page ? $this->formatSections($page->sections) : [];

        // Gather pet categories (children of "pets" or any category with slug/name containing "pet")
        $categories = Category::where('status', 1)->get(['id', 'parent_id', 'name', 'slug']);
        $petsParent = $categories->first(function ($c) {
            $slug = strtolower($c->slug ?? '');
            $name = strtolower($c->name ?? '');
            return $slug === 'pets' || $name === 'pets';
        });

        $petChildren = $categories->filter(function ($c) use ($petsParent) {
            if ($petsParent && $c->parent_id === $petsParent->id) {
                return true;
            }
            $slug = strtolower($c->slug ?? '');
            $name = strtolower($c->name ?? '');
            return str_contains($slug, 'pet') || str_contains($name, 'pet');
        })->values();

        $allowedCategoryIds = $petChildren->pluck('id')->all();

        $products = ProductResource::collection(
            $this->buildQuery($request, $allowedCategoryIds)->get()
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
            'categories' => $petChildren->all(),
            'products' => $products,
        ]);
    }

    protected function buildQuery(Request $request, array $allowedCategoryIds): Builder
    {
        $query = Product::query()
            ->with(['category', 'mediaFeatured'])
            ->whereIn('category_id', $allowedCategoryIds ?: [-1]) // if none, no products
            ->when(!$request->boolean('include_inactive'), fn(Builder $b) => $b->where('status', 1))
            ->when($request->boolean('featured'), fn(Builder $b) => $b->where('featured', 1))
            ->when($request->boolean('new'), fn(Builder $b) => $b->where('new', 1))
            ->when($request->boolean('top'), fn(Builder $b) => $b->where('top', 1))
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
