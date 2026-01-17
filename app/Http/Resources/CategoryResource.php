<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $featuredMediaPath = $this->mediaFeatured?->path;
        $allMedia = $this->media ?? collect();
        
        // Get featured image URL using proper resolution
        // If no featured media, use first media item
        if (!$featuredMediaPath && $allMedia->count() > 0) {
            $featuredMediaPath = $allMedia->first()?->path;
        }
        $featuredImageUrl = $featuredMediaPath ? self::resolveImageUrl($featuredMediaPath) : null;
        
        // Get second image (first non-featured media, or second media if featured is first)
        $secondImageUrl = null;
        if ($allMedia && $allMedia->count() > 1) {
            // If we have a featured image, get the first non-featured one
            if ($featuredMediaPath) {
                $secondMedia = $allMedia->where('path', '!=', $featuredMediaPath)->first();
            } else {
                // If no featured, get the second one
                $secondMedia = $allMedia->skip(1)->first();
            }
            if ($secondMedia && $secondMedia->path) {
                $secondImageUrl = self::resolveImageUrl($secondMedia->path);
            }
        } elseif ($allMedia && $allMedia->count() === 1 && $featuredMediaPath) {
            // If only one image and it's featured, no second image
            $secondImageUrl = null;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $featuredImageUrl,
            'second_image_url' => $secondImageUrl,
        ];
    }

    /**
     * Resolve image URL - same logic as ProductResource and HomeController
     * Ensures full absolute URL is returned
     */
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
