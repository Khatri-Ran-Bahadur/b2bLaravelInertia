<?php

namespace App\Http\Resources\Tender;

use App\Http\Resources\Api\CompanyDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderListResource extends JsonResource
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
            'location' => $this->location,
            'created_at' => $this->created_at,
            'company' => new CompanyDetailResource($this->whenLoaded('company')),
            'tender_category' => new TenderCategoryResource($this->whenLoaded('tenderCategory')),
            'main_image' => $this->whenLoaded('media', function () {
                $media = $this->getFirstMedia('tender_images');
                return $media ? [
                    'id' => $media->id,
                    'name' => $media->name,
                    'url' => $media->getUrl(),
                ] : null;
            }),
        ];
    }
}
