<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'stock_movements_count' => $this->whenCounted('stockMovements'),
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
