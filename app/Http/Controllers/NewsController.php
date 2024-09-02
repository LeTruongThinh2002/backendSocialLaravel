<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::user();

        // Lọc các bài viết
        $news = News::whereNotIn('user_id', function ($query) use ($user) {
            $query->select('user_blocked')
                ->from('user_block')
                ->where('user_id', $user->id);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return NewsResource::collection($news);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        // Tạo bài viết mới
        $news = News::create($request->validated());

        return new NewsResource($news);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return new NewsResource($news);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        // Kiểm tra quyền sở hữu
        if ($news->user_id !== JWTAuth::user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $news->update($request->validated());
        return new NewsResource($news);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        // Kiểm tra quyền sở hữu
        if ($news->user_id !== JWTAuth::user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $news->delete();
        return response()->json(null, 204);
    }

    // Lấy news từ những người dùng đang follow
    public function latestNews()
    {
        $user = JWTAuth::user();

        $news = News::whereIn('user_id', function ($query) use ($user) {
            $query->select('user_following')
                ->from('user_follow')
                ->where('user_id', $user->id);
        })
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->orderBy('created_at', 'desc')
            ->get();

        return NewsResource::collection($news);
    }

    // Lấy tất cả news của người dùng chỉ định
    public function getUserNews(User $getUser)
    {
        $news = $getUser->news()
            ->orderBy('created_at', 'desc')
            ->get();

        return NewsResource::collection($news);
    }
}
