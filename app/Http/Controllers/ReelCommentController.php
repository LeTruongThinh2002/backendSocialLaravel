<?php

namespace App\Http\Controllers;

use App\Models\ReelComment;
use App\Http\Requests\StoreReelCommentRequest;
use App\Http\Requests\UpdateReelCommentRequest;
use App\Http\Resources\ReelCommentResource;
use App\Models\Reel;
use App\Models\ReelsComment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class ReelCommentController extends Controller
{
    public function index(Reel $reel)
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

        $comments = ReelsComment::where('reels_id', $reel->id)
            ->whereNull('parent_comment_id')
            ->whereNotIn('user_id', $blockedUsers)
            ->with([
                'commentReply' => function ($query) use ($blockedUsers) {
                    $query->whereNotIn('user_id', $blockedUsers);
                }
            ])
            ->paginate(10);

        return ReelCommentResource::collection($comments);
    }

    public function like(ReelsComment $comment)
    {
        $user = JWTAuth::user();
        $reel = $comment->commentInReels;

        // Kiểm tra cả hai chiều block
        $isBlocked = $user->userBlock()
            ->where('user_blocked', $reel->user_id)
            ->exists() ||
            $user->userBlockedBy()
                ->where('user_id', $reel->user_id)
                ->exists();

        if ($isBlocked) {
            return response()->json(['error' => 'You cannot interact with this content due to block restrictions.'], 403);
        }

        $comment->commentLike()->toggle($user->id);
        return response()->json(ReelCommentResource::make($comment), 200);
    }

    public function store(StoreReelCommentRequest $request)
    {
        $user = JWTAuth::user();
        $comment = new ReelsComment($request->validated());
        $comment->user_id = $user->id;
        $comment->save();

        return new ReelCommentResource($comment);
    }

    public function show(ReelsComment $comment)
    {
        return new ReelCommentResource($comment);
    }

    public function update(UpdateReelCommentRequest $request, ReelsComment $comment)
    {
        $user = JWTAuth::user();
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update($request->validated());
        return new ReelCommentResource($comment);
    }

    public function destroy(ReelsComment $comment)
    {
        $user = JWTAuth::user();
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(null, 204);
    }
}
