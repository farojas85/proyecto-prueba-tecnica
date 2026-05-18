<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'type' => $this->type,
            'quantity' => (int) $this->quantity,
            'reason' => $this->reason,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
