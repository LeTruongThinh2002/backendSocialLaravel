<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Post;
use App\Models\Reel;
use Illuminate\Support\Facades\Log;

class CheckUserBlock
{
    public function handle(Request $request, Closure $next)
    {
        $user = JWTAuth::user();
        $targetUserId = $this->getTargetUserId($request);

        if ($targetUserId) {
            $blockCount = $user->userBlock->where('id', $targetUserId)->count();
            $blockedByCount = $user->userBlockedBy->where('id', $targetUserId)->count();

            if ($blockCount > 0 || $blockedByCount > 0) {
                return response()->json(['error' => 'Action not allowed due to block restrictions'], 403);
            }
        }

        return $next($request);
    }

    private function getTargetUserId(Request $request)
    {
        if ($request->route('getUser')) {
            return $request->route('getUser')->id;
        } elseif ($request->route('post')) {
            return $request->route('post')->user_id;
        } elseif ($request->route('comment')) {
            return $request->route('comment')->user_id;
        } elseif ($request->route('reel')) {
            return $request->route('reel')->user_id;
        } elseif ($request->route('news')) {
            return $request->route('news')->user_id;
        } elseif ($request->input('post_id')) {
            $post = Post::find($request->input('post_id'));
            return $post ? $post->user_id : null;
        } elseif ($request->input('reels_id')) {
            $reels = Reel::find($request->input('reels_id'));
            return $reels ? $reels->user_id : null;
        } elseif ($request->route('user')) {
            return $request->route('user')->id;
        }

        return null;
    }
}
