<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'user' => new UserRelationalReosurce($this->whenLoaded('user')),
            'product' => new ProductListResource($this->whenLoaded('product')),
            'review' => $this->review,
            'rating' => $this->rating,
            'is_approved' => $this->is_approved,
            'images' => $this->getMedia('images')->map(function ($media) {
                return $media->getUrl();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'answers' => ReviewAnswerResource::collection($this->answers)
        ];
    }
}
