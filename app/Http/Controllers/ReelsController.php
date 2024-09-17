<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReelsResource;
use App\Models\Reel;
use App\Http\Requests\StoreReelRequest;
use App\Http\Requests\UpdateReelRequest;
use App\Models\User;
use Carbon\Carbon;
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
        $reels = Reel::select('reels.id', 'reels.user_id', 'reels.description', 'reels.created_at', 'reels.updated_at')
            ->leftJoin('user_follow', function ($join) use ($user) {
                $join->on('user_follow.user_following', '=', 'reels.user_id')
                    ->where('user_follow.user_id', $user->id);
            })
            ->whereNotNull('user_follow.user_id')
            ->where('reels.created_at', '<=', Carbon::now()->subDays(3))
            ->with([
                'reelsUser:id,first_name,last_name,avatar',
                'reelsComment:reels_id',
                'reelsLike:id,first_name,last_name,avatar',
                'reelsMedia:reels_id,media'
            ])
            ->orderBy('reels.created_at', 'desc')
            ->paginate(5);

        return ReelsResource::collection($reels);
    }

    // Toggle like reel
    public function toggleLikeReel(Reel $reel)
    {
        $user = JWTAuth::user();
        $reel->reelsLike()->toggle($user->id);
        $reel->load('reelsLike');
        return new ReelsResource($reel);
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
        $reels = Reel::select('reels.id', 'reels.user_id', 'reels.description', 'reels.media', 'reels.created_at', 'reels.updated_at')
            ->where('reels.user_id', $getUser->id)
            ->with([
                'reelsUser:id,first_name,last_name,avatar',
                'reelsComment:reels_id',
                'reelsLike:id,first_name,last_name,avatar',

            ])
            ->latest('reels.created_at')
            ->get();

        return ReelsResource::collection($reels);
    }
}
