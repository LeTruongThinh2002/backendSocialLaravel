<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsResource;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\PostsMedia;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;


class PostsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::user();

        // Lọc các bài viết từ những người dùng không được theo dõi và không bị chặn
        $posts = Post::whereNotIn('user_id', function ($query) use ($user) {
            $query->select('user_following')
                ->from('user_follow')
                ->where('user_id', $user->id);
        })
            // Lọc các bài viết từ những người dùng đã bị chặn
            ->whereNotIn('user_id', function ($query) use ($user) {
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

        return PostsResource::collection($posts);
    }

    public function followedPosts()
    {
        $user = JWTAuth::user();

        // Lọc các bài viết từ những người dùng mà người đăng nhập đang theo dõi
        $posts = Post::whereIn('user_id', function ($query) use ($user) {
            $query->select('user_following')
                ->from('user_follow')
                ->where('user_id', $user->id);
        })
            ->where('created_at', '<=', Carbon::now()->subDays(3))
            ->orderBy('created_at', 'desc')
            ->get();

        return PostsResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Tạo bài viết mới
        $post = Post::create($request->validated());

        // Lưu các liên kết media
        if ($request->has('media')) {
            foreach ($request->input('media') as $mediaUrl) {
                PostsMedia::create([
                    'post_id' => $post->id,
                    'media' => $mediaUrl,
                ]);
            }
        }

        return new PostsResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostsResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // Kiểm tra quyền sở hữu
        if ($post->user_id !== JWTAuth::user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->update($request->validated());
        return new PostsResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Kiểm tra quyền sở hữu
        if ($post->user_id !== JWTAuth::user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();
        return response()->json(null, 204);
    }

    // Lấy 9 bài viết có số lượng like nhiều nhất
    public function topPosts(User $getUser)
    {
        $posts = Post::where('user_id', $getUser->id)
            ->withCount('postsLike')
            ->orderBy('posts_like_count', 'desc')
            ->take(9)
            ->get();

        return PostsResource::collection($posts);
    }

    // Lấy tất cả bài viết của người dùng chỉ định
    public function getUserPosts(User $getUser)
    {
        $posts = $getUser->posts()
            ->orderBy('created_at', 'desc')
            ->get();

        return PostsResource::collection($posts);
    }
}
