<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'image_url' => $featuredMediaPath ? asset('storage/' . ltrim($featuredMediaPath, '/')) : null,
            'affiliate_link' => data_get($this, 'affiliate_link'),
            'amazon_link' => data_get($this, 'amazon_link'),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
