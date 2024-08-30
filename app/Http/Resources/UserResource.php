<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userFollowIds = $this->userFollow->pluck('id')->toArray();
        $userFollowerIds = $this->userFollower->pluck('id')->toArray();
        $friends = array_intersect($userFollowIds, $userFollowerIds);
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "email_verify_at" => $this->email_verified_at
                ? $this->email_verified_at->format('Y-m-d H:i:s')
                : null,
            "avatar" => $this->avatar,
            "background" => $this->background,
            "userFollow" => $this->userFollow->pluck('id'),
            "friends" => $friends,
            "userBlock" => $this->userBlock->pluck('id'),
        ];
    }
}
