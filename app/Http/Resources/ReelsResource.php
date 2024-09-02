<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReelsResource extends JsonResource
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
            'user' => $this->reelsUser,
            // 'comments' => $this->postsComment,
            'description' => $this->description,
            'media' => $this->media, // Lấy danh sách các liên kết media
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
