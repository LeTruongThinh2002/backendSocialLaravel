<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::options('{any}', function (Request $request) {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
})->where('any', '.*');
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::get('fetchProfile', [AuthController::class, 'fetchProfile'])->middleware('auth:api')->name('fetchProfile');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('verify', [AuthController::class, 'resendVerificationEmail'])->name('verify');
    Route::post('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified.'], 200);
    })->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('forgotPassword', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');
});
Route::get('/users/{user}', [UsersController::class, 'getProfile'])->name('users.getProfile');

<<<<<<< Updated upstream
Route::put('/users/{user}', [UsersController::class, 'update'])->middleware(['auth:api'])->name('users.update');
Route::delete('/users/{user}', [UsersController::class, 'destroy'])->middleware(['auth:api'])->name('users.destroy');
Route::put('/users/changePwd/{user}', [UsersController::class, 'changePassword'])->middleware(['auth:api'])->name('users.changePwd');
Route::put('/users/changeEmail/{user}', [UsersController::class, 'changeEmail'])->middleware(['auth:api'])->name('users.changeEmail');
=======
// Route cho UsersController
Route::group(['middleware' => 'auth:api'], function () {
    // Lấy thông tin user chỉ định
    Route::get('/users/{user}', [UsersController::class, 'getProfile'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('users.getProfile');
    // Cập nhật thông tin user
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    // Xóa user
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    // Cập nhật mật khẩu
    Route::put('/users/changePwd/{user}', [UsersController::class, 'changePassword'])->name('users.changePwd');
    // Cập nhật email
    Route::put('/users/changeEmail/{user}', [UsersController::class, 'changeEmail'])->name('users.changeEmail');
    // Block or unblock user
    Route::post('/users/block/{user}', [UsersController::class, 'toggleBlockUser'])->name('users.toggleBlockUser');
});

// Routes for PostsController
Route::group(['middleware' => 'auth:api'], function () {
    // Lọc ra post của user không follow hay bị block
    Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');
    // Tạo post mới
    Route::post('/posts', [PostsController::class, 'store'])->name('posts.store');
    // Lấy post của user đang follow
    Route::get('/posts/followedPosts', [PostsController::class, 'followedPosts'])
        ->name('posts.followedPosts');
    // Lấy post chỉ định
    Route::get('/posts/{post}', [PostsController::class, 'show'])
        ->middleware(\App\Http\Middleware\CheckUserBlock::class)
        ->name('posts.show');
    // Cập nhật post
    Route::put('/posts/{post}', [PostsController::class, 'update'])->name('posts.update');
    // Xóa post
    Route::delete('/posts/{post}', [PostsController::class, 'destroy'])->name('posts.destroy');
    // Lấy top post của user chỉ định
    Route::get('/posts/{getUser}/top', [PostsController::class, 'topPosts'])
        ->name('posts.topPosts');
    // Lấy post của user chỉ định
    Route::get('/posts/{getUser}/getPosts', [PostsController::class, 'getUserPosts'])
        ->middleware(\App\Http\Middleware\CheckUserBlock::class)
        ->name('posts.getUserPosts');
});

// Routes for ReelsController
Route::group(['middleware' => 'auth:api'], function () {
    // Lấy danh sách reel mới nhất
    Route::get('/reels', [ReelsController::class, 'index'])->name('reels.index');
    // Tạo reel mới
    Route::post('/reels', [ReelsController::class, 'store'])->name('reels.store');
    // Lấy reel chỉ định
    Route::get('/reels/{reel}', [ReelsController::class, 'show'])
        ->middleware(\App\Http\Middleware\CheckUserBlock::class)
        ->name('reels.show');
    // Cập nhật reel
    Route::put('/reels/{reel}', [ReelsController::class, 'update'])->name('reels.update');
    // Xóa reel
    Route::delete('/reels/{reel}', [ReelsController::class, 'destroy'])->name('reels.destroy');
    // Lấy reel của user chỉ định
    Route::get('/reels/{getUser}/getReels', [ReelsController::class, 'getUserReels'])
        ->middleware(\App\Http\Middleware\CheckUserBlock::class)
        ->name('reels.getUserReels');
});

// Routes for NewsController
Route::group(['middleware' => 'auth:api'], function () {
    // Tạo tin tức mới
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    // Lấy tin tức mới nhất từ user đang follow
    Route::get('/news/latestNews', [NewsController::class, 'latestNews'])
        ->name('news.latestNews');
    // Lấy tin tức chỉ định
    Route::get('/news/{news}', [NewsController::class, 'show'])
        ->middleware(\App\Http\Middleware\CheckUserBlock::class)
        ->name('news.show');
    // Cập nhật tin tức
    Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
    // Xóa tin tức
    Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
    // Lấy tin tức của user chỉ định
    Route::get('/news/{getUser}/user', [NewsController::class, 'getUserNews'])
        ->middleware(\App\Http\Middleware\CheckUserBlock::class)
        ->name('news.getUserNews');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/posts/{post}/comments', [PostCommentController::class, 'index'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('posts.comments.index');
    // Tạo comment
    Route::post('/posts/comments', [PostCommentController::class, 'store'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('posts.comments.store');
    // Lấy 1 comment
    // Route::get('/posts/comments/{comment}', [PostCommentController::class, 'show'])->name('posts.comments.show');
    // Like comment
    Route::post('/posts/comments/like/{comment}', [PostCommentController::class, 'like'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('posts.comments.like');
    // Cập nhật comment
    Route::put('/posts/comments/{comment}', [PostCommentController::class, 'update'])->name('posts.comments.update');
    // Xóa comment
    Route::delete('/posts/comments/{comment}', [PostCommentController::class, 'destroy'])->name('posts.comments.destroy');
});

// Routes for ReelCommentController
Route::group(['middleware' => 'auth:api'], function () {
    // Lấy comment của reel
    Route::get('/reels/{reel}/comments', [ReelCommentController::class, 'index'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('reels.comments.index');
    // Tạo comment
    Route::post('/reels/comments', [ReelCommentController::class, 'store'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('reels.comments.store');
    // Like comment
    Route::post('/reels/comments/like/{comment}', [ReelCommentController::class, 'like'])->middleware(\App\Http\Middleware\CheckUserBlock::class)->name('reels.comments.like');
    // Cập nhật comment
    Route::put('/reels/comments/{comment}', [ReelCommentController::class, 'update'])->name('reels.comments.update');
    // Xóa comment
    Route::delete('/reels/comments/{comment}', [ReelCommentController::class, 'destroy'])->name('reels.comments.destroy');
});
>>>>>>> Stashed changes


