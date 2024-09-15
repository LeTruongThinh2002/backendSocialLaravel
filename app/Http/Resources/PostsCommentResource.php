<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsCommentResource extends JsonResource
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
            'post_id' => $this->post_id,
            'parent_comment_id' => $this->parent_comment_id,
            'user' => $this->commentUser->only(['id', 'first_name', 'last_name', 'avatar']),
            'comment' => $this->comment,
            'like' => $this->commentLike->map(function ($user) {
                return $user->only(['id', 'first_name', 'last_name', 'avatar']);
            }),
            'reply' => PostsCommentResource::collection($this->commentReply),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
