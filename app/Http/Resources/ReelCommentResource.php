<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReelCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reels_id' => $this->reels_id,
            'parent_comment_id' => $this->parent_comment_id,
            'user' => $this->commentUser->only(['id', 'first_name', 'last_name', 'avatar']),
            'comment' => $this->comment,
            'like' => $this->commentLike->count(),
            'reply' => ReelCommentResource::collection($this->commentReply),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
