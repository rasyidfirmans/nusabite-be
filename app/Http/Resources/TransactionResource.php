<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "status" => $this->status,
            "invoice_id" => $this->invoice_id,
            "created_at" => $this->created_at,
            "total" => $this->products->sum(function ($product) {
                return $product->price * $product->pivot->quantity;
            }),
            "products" => $this->products->map(function ($product) {
                return [
                    "id" => $product->id,
                    "name" => $product->name,
                    "category" => $product->category->name,
                    "description" => $product->description,
                    "price" => $product->price,
                    "image" => $product->image,
                    "quantity" => $product->pivot->quantity,
                ];
            }),
        ];
    }
}
