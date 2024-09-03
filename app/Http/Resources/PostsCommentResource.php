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
            'user' => [
                'id' => $this->commentUser->id,
                'first_name' => $this->commentUser->first_name,
                'last_name' => $this->commentUser->last_name,
                'avatar' => $this->commentUser->avatar,
            ],
            'comment' => $this->comment,
            'like' => $this->commentLike->pluck('id'),
            'reply' => PostsCommentResource::collection($this->commentReply),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
