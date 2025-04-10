<?php

namespace App\Http\Resources\Tender;

use App\Http\Resources\Api\CompanyDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company' => new CompanyDetailResource($this->whenLoaded('company')),
            'tender_category' => new TenderCategoryResource($this->whenLoaded('tenderCategory')),
        ];
    }
}
