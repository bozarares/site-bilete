<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_uuid' => $this->user_uuid,
            'items' => CartItemResource::collection($this->items),
        ];
    }
}
