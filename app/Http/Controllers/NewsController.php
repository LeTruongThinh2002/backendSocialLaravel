<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Models\news;
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
        $news = news::whereNotIn('user_id', function ($query) use ($user) {
            $query->select('user_blocked')
                ->from('user_block')
                ->where('user_id', $user->id);
        })
            ->orderBy('created_at', 'desc')
            ->get();

        return NewsResource::collection($news);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        try { // Tạo bài viết mới
            $user = JWTAuth::user();
            $validatedData = $request->validated();
            $validatedData['user_id'] = $user->id;
            $news = news::create($validatedData);

            return new NewsResource($news);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(news $news)
    {
        return new NewsResource($news);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, news $news)
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
    public function destroy(news $news)
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
        $threeDaysAgo = Carbon::now()->subDays(3);

        $groupedNews = news::query()
            // lấy thông tin user
            ->join('users', 'news.user_id', '=', 'users.id')
            // chỉ lấy news từ những người dùng đang follow
            ->joinSub(
                $user->userFollow(),
                'user_follow',
                function ($join) {
                    $join->on('news.user_id', '=', 'user_follow.id');
                }
            )
            // chỉ lấy news trong 3 ngày gần đây nhất
            ->where('news.created_at', '<=', $threeDaysAgo)
            // lấy thông tin cần thiết từ user và news
            ->select(
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
                'users.avatar',
                'news.id as news_id',
                'news.description',
                'news.media',
                'news.created_at'
            )
            // sắp xếp theo ngày tạo news
            ->orderBy('news.created_at', 'desc')
            ->get()
            // nhóm theo user_id
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'user' => [
                        'id' => $group->first()->user_id,
                        'first_name' => $group->first()->first_name,
                        'last_name' => $group->first()->last_name,
                        'avatar' => $group->first()->avatar,
                    ],
                    'news' => $group->map(function ($item) {
                        return [
                            'id' => $item->news_id,
                            'description' => $item->description,
                            'media' => $item->media,
                            'created_at' => $item->created_at,
                        ];
                    }),
                ];
            })
            ->values()
            ->all();

        return response()->json($groupedNews);
    }

    // Lấy tất cả news của người dùng chỉ định
    public function getUserNews(User $getUser)
    {
        // Sử dụng join để lấy thông tin news cùng với thông tin user trong một truy vấn
        $news = news::query()
            ->join('users', 'news.user_id', '=', 'users.id')
            ->where('news.user_id', $getUser->id)
            ->select(
                'news.id',
                'news.user_id',
                'news.media',
                'news.description',
                'news.created_at',
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
                'users.avatar'
            )
            ->latest('news.created_at')->get()->groupBy('user_id')
            ->map(function ($item) {
                return [
                    'user' => [
                        'id' => $item->first()->user_id,
                        'first_name' => $item->first()->first_name,
                        'last_name' => $item->first()->last_name,
                        'avatar' => $item->first()->avatar,
                    ],
                    'news' => $item->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'description' => $item->description,
                            'media' => $item->media,
                            'created_at' => $item->created_at,
                        ];
                    })
                ];
            })->values()->all();

        return response()->json($news);

    }
}
