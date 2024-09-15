<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userFollowIds = $this->userFollow;
        $userFollowerIds = $this->userFollower;
        $friends = $userFollowIds->intersect($userFollowerIds)->values();
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
            "userFollow" => $this->limitUserFields($this->userFollow),
            "userFollower" => $this->limitUserFields($this->userFollower),
            "friends" => $this->limitUserFields($friends),
            "userBlock" => $this->limitUserFields($this->userBlock),
            "countPost" => $this->posts->count(),
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function limitUserFields(Collection $users)
    {
        return $users->map(function ($user) {
            return $user->only(['id', 'first_name', 'last_name', 'avatar']);
        });
    }


}
