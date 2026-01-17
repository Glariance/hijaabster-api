<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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

        // Format coupon discount text
        $couponDiscountText = null;
        if ($this->coupon) {
            if ($this->coupon->discount_type === 'percentage') {
                $couponDiscountText = $this->coupon->discount_value . '% off';
            } else {
                $couponDiscountText = 'PKR ' . number_format($this->coupon->discount_value, 2) . ' off';
            }
        }

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->base_price,
            'has_variations' => (bool) $this->has_variations,
            'featured' => (bool) $this->featured,
            'top' => (bool) $this->top,
            'is_new' => (bool) $this->new,
            'status' => (bool) $this->status,
            'image_url' => $featuredImageUrl,
            'second_image_url' => $secondImageUrl,
            'affiliate_link' => data_get($this, 'affiliate_link'),
            'amazon_link' => data_get($this, 'amazon_link'),
            'coupon' => $this->coupon ? [
                'id' => $this->coupon->id,
                'name' => $this->coupon->name,
                'code' => $this->coupon->code,
                'discount_type' => $this->coupon->discount_type,
                'discount_value' => (float) $this->coupon->discount_value,
                'discount_text' => $couponDiscountText,
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Resolve image URL - same logic as HomeController
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
