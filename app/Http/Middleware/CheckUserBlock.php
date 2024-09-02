<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckUserBlock
{
    public function handle(Request $request, Closure $next)
    {
        $user = JWTAuth::user();
        $targetUserId = $request->route('getUser')->id
            ?? $request->route('post')->user_id
            ?? $request->route('reel')->user_id
            ?? $request->route('news')->user_id;

        $blockCount = $user->userBlock->where('id', $targetUserId)->count();
        if ($blockCount > 0) {
            return response()->json(['error' => 'You are blocked by this user'], 403);
        }

        return $next($request);
    }
}
