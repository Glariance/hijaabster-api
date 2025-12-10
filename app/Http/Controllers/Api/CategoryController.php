<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Return active categories for the storefront.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->with('media')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
