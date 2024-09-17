<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsResource;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\PostsMedia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;


class PostsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::user();

        $posts = Post::select('posts.id', 'posts.user_id', 'posts.description', 'posts.created_at', 'posts.updated_at')
            // Lấy các bài viết của người dùng mà người đăng nhập đang theo dõi
            ->leftJoin('user_follow', function ($join) use ($user) {
                $join->on('user_follow.user_following', '=', 'posts.user_id')
                    ->where('user_follow.user_id', $user->id);
            })
            // Lấy các bài viết của người dùng mà người đăng nhập đang block
            ->leftJoin('user_block as block1', function ($join) use ($user) {
                $join->on('block1.user_blocked', '=', 'posts.user_id')
                    ->where('block1.user_id', $user->id);
            })
            // Lấy các bài viết của người dùng mà đang block người đăng nhập
            ->leftJoin('user_block as block2', function ($join) use ($user) {
                $join->on('block2.user_id', '=', 'posts.user_id')
                    ->where('block2.user_blocked', $user->id);
            })
            // lọc bỏ các bài viết của người dùng đang theo dõi, bị block và block người dùng đăng nhập
            ->whereNull('user_follow.user_id')
            ->whereNull('block1.user_id')
            ->whereNull('block2.user_id')
            // Lấy thông tin người dùng, bình luận, like và media của bài viết
            ->with([
                'postsUser:id,first_name,last_name,avatar',
                'postsComment:post_id',
                'postsLike:id,first_name,last_name,avatar',
                'postsMedia:post_id,media'
            ])
            ->latest('posts.created_at')
            ->paginate(5);

        return PostsResource::collection($posts);
    }

    public function followedPosts()
    {
        // $user = JWTAuth::user();

        // Lọc các bài viết từ những người dùng mà người đăng nhập đang theo dõi
        $user = JWTAuth::user()->load('userFollow');

        $posts = Post::select('posts.id', 'posts.user_id', 'posts.description', 'posts.created_at', 'posts.updated_at')
            ->leftJoin('user_follow', function ($join) use ($user) {
                $join->on('user_follow.user_following', '=', 'posts.user_id')
                    ->where('user_follow.user_id', $user->id);
            })
            ->whereNotNull('user_follow.user_id')
            ->where('posts.created_at', '<=', Carbon::now()->subDays(3))
            ->with([
                'postsUser:id,first_name,last_name,avatar',
                'postsComment:post_id',
                'postsLike:id,first_name,last_name,avatar',
                'postsMedia:post_id,media'
            ])
            ->orderBy('posts.created_at', 'desc')
            ->paginate(5);
        return PostsResource::collection($posts);
    }

    public function toggleLikePost(Post $post)
    {
        $user = JWTAuth::user();
        $post->postsLike()->toggle($user->id);
        $post->load('postsLike');
        return new PostsResource($post);
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
        $posts = Post::select('posts.id', 'posts.user_id', 'posts.description', 'posts.created_at', 'posts.updated_at')
            ->where('posts.user_id', $getUser->id)
            ->with([
                'postsUser:id,first_name,last_name,avatar',
                'postsComment:post_id',
                'postsLike:id,first_name,last_name,avatar',
                'postsMedia:post_id,media'
            ])
            ->latest('posts.created_at')
            ->get();

        return PostsResource::collection($posts);
    }
}
