<?php

namespace App\Http\Resources\Tender;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderCategoryListResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
