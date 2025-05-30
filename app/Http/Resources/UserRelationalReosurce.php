<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRelationalReosurce extends JsonResource
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
            'name' => (string)$this->name,
            'email' => (string)$this->email,
            'phone' => $this->phone,
            'image' => $this->image ? asset($this->image) : '',
            'status' => $this->status
        ];
    }
}
