<?php

namespace App\Http\Resources\Tender;

use App\Http\Resources\Api\CompanyDetailResource;
use App\Http\Resources\CompanyRelationalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderDetailResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'budget_from' => $this->budget_from,
            'budget_to' => $this->budget_to,
            'phone' => $this->phone,
            'email' => $this->email,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company' => new CompanyRelationalResource($this->whenLoaded('company')),
            'tender_category' => new TenderCategoryResource($this->whenLoaded('tenderCategory')),
            'tender_products' => $this->whenLoaded('tenderProducts', function () {
                return $this->tenderProducts->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'quantity' => $product->quantity,
                        'unit' => $product->unit,
                        'description' => $product->description,
                    ];
                });
            }),
            'media' => $this->whenLoaded('media', function () {
                return $this->getMedia('tender_images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                    ];
                });
            }),
        ];
    }
}
