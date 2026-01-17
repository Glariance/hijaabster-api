<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Calculate savings percentage
        $totalPrice = (float) ($this->total_price ?? 0);
        $bundlePrice = (float) ($this->bundle_price ?? 0);
        $savings = $totalPrice > 0 ? round((($totalPrice - $bundlePrice) / $totalPrice) * 100) : 0;
        $savingsText = $savings > 0 ? "Save {$savings}%" : "Save PKR " . number_format($totalPrice - $bundlePrice, 2);

        // Format product inclusions
        $inclusions = $this->products->map(function ($product) {
            $quantity = $product->pivot->quantity ?? 1;
            return "{$quantity} " . strtolower($product->name);
        })->toArray();

        // Format description - strip HTML tags and decode entities
        $description = strip_tags($this->description ?? '');
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $description,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'total_price' => $totalPrice,
            'bundle_price' => $bundlePrice,
            'savings' => $savings,
            'savings_text' => $savingsText,
            'status' => (bool) $this->status,
            'inclusions' => $inclusions,
            'products_count' => $this->products->count(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

