<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\UserRelationalReosurce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class CompanyDetailResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $owner = $this->owner->first();
        return [
            'id' => $this->id,
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'logo' => (string) $this->logo,
            'phone' => (string) $this->phone,
            'address' => (string) $this->address,
            'verification_status' => $this->verification_status,
            'tin_number' => (string) $this->tin_number,
            'created_at' => $this->created_at,
            'created_at' => $this->created_at->toDateTimeString(),
            'owner' => $owner ? new UserRelationalReosurce($owner) : new stdClass,
        ];
    }
}
