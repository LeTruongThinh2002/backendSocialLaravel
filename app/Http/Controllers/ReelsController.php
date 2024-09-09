<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReelsResource;
use App\Models\Reel;
use App\Http\Requests\StoreReelRequest;
use App\Http\Requests\UpdateReelRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::user();

        // Lọc các reels từ những người dùng đã bị chặn
        $reels = Reel::whereNotIn('user_id', function ($query) use ($user) {
            $query->select('user_blocked')
                ->from('user_block')
                ->where('user_id', $user->id);
        })
            // Lọc các bài viết từ những người dùng đã chặn người đăng nhập
            ->whereNotIn('user_id', function ($query) use ($user) {
                $query->select('user_id')
                    ->from('user_block')
                    ->where('user_blocked', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return ReelsResource::collection($reels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReelRequest $request)
    {
        // Tạo reel mới
        $reel = Reel::create($request->validated());

        return new ReelsResource($reel);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reel $reel)
    {
        return new ReelsResource($reel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReelRequest $request, Reel $reel)
    {
        // Kiểm tra quyền sở hữu
        if ($reel->user_id !== JWTAuth::user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reel->update($request->validated());
        return new ReelsResource($reel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reel $reel)
    {
        // Kiểm tra quyền sở hữu
        if ($reel->user_id !== JWTAuth::user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reel->delete();
        return response()->json(null, 204);
    }

    // Lấy tất cả reels của người dùng chỉ định
    public function getUserReels(User $getUser)
    {
        $reels = $getUser->reels()
            ->orderBy('created_at', 'desc')
            ->get();

        return ReelsResource::collection($reels);
    }
}
