<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProfileResource extends JsonResource
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
            "date_of_birth" => $this->date_of_birth,
            "avatar" => $this->avatar,
            "background" => $this->background,
            "country" => $this->country,
            "userFollow" => $this->userFollow->pluck('id'),
            "userFollower" => $this->userFollower->pluck('id'),
            "friends" => $friends,
            "userBlock" => $this->userBlock->pluck('id'),
            "countPost" => $this->posts->count(),
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

}
