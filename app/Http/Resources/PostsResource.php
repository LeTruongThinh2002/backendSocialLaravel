<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->postsUser,
            'comments' => $this->postsComment->count(),
            'description' => $this->description,
            'media' => $this->postsMedia->pluck('media'), // Lấy danh sách các liên kết media
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}