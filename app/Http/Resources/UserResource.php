<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="User Resource",
 *     description="Represents a user in the system",
 *     @OA\Property(property="id", type="integer", example=1, description="The unique identifier of the user"),
 *     @OA\Property(property="name", type="string", example="John Doe", description="The name of the user"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com", description="The email address of the user"),
 *     @OA\Property(property="phone", type="string", example="+996123456789", description="The phone number of the user"),
 *     @OA\Property(property="user_role", type="string", example="admin", description="The role assigned to the user")
 * )
 */
class UserResource extends JsonResource
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
            'name' => $this->name ?? '',
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'username' => $this->username ?? '',
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'user_role' => $this->user_role,
            "status" => $this->status,
            'image' => $this->image ? asset($this->image) : '',
            'fcm_token' => $this->fcm_token ?? '',
            'firebase_id' => $this->firebase_id ?? '',
            'google_id' => $this->google_id ?? '',
            'apple_id' => $this->apple_id ?? '',

        ];
    }
}
