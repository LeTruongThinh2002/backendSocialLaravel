<?php

namespace App\Http\Controllers;

use App\Models\PostsComment;
use App\Http\Requests\StorePostsCommentRequest;
use App\Http\Requests\UpdatePostsCommentRequest;
use App\Http\Resources\PostsCommentResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserBlock;
use Illuminate\Support\Facades\DB;

class PostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        $user = JWTAuth::user();

        $blockedUsers = DB::table('user_block')
            ->where('user_id', $user->id)
            ->select('user_blocked as id')
            ->union(
                DB::table('user_block')
                    ->where('user_blocked', $user->id)
                    ->select('user_id as id')
            );

        $comments = PostsComment::where('post_id', $post->id)
            ->whereNull('parent_comment_id')
            ->whereNotIn('user_id', $blockedUsers)
            ->with([
                'commentReply' => function ($query) use ($blockedUsers) {
                    $query->whereNotIn('user_id', $blockedUsers);
                }
            ])
            ->paginate(10);

        return PostsCommentResource::collection($comments);
    }

    public function like(PostsComment $comment)
    {
        $user = JWTAuth::user();
        $post = $comment->commentInPosts;

        // Kiểm tra cả hai chiều block
        $isBlocked = $user->userBlock()
            ->where('user_blocked', $post->user_id)
            ->exists() ||
            $user->userBlockedBy()
                ->where('user_id', $post->user_id)
                ->exists();

        if ($isBlocked) {
            return response()->json(['error' => 'You cannot interact with this content due to block restrictions.'], 403);
        }

        $comment->commentLike()->toggle($user->id);
        return response()->json(PostsCommentResource::make($comment), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostsCommentRequest $request)
    {
        $user = JWTAuth::user();
        $comment = new PostsComment($request->validated());
        $comment->user_id = $user->id;
        $comment->save();

        return new PostsCommentResource($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostsComment $comment)
    {
        return new PostsCommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostsCommentRequest $request, PostsComment $comment)
    {
        $user = JWTAuth::user();
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update($request->validated());
        return new PostsCommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostsComment $comment)
    {
        $user = JWTAuth::user();
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(null, 200);
    }
}
