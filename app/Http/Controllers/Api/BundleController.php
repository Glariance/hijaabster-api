<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Models\Bundle;
use Illuminate\Http\JsonResponse;

class BundleController extends Controller
{
    /**
     * Get all active bundles.
     */
    public function index(): JsonResponse
    {
        $bundles = Bundle::where('status', 1)
            ->with('products')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => BundleResource::collection($bundles),
        ]);
    }
}

