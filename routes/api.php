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

    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:api')->name('profile');
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
Route::get('/users/{user}', [UsersController::class, 'getUser'])->name('users.getUser');

Route::put('/users/{user}', [UsersController::class, 'update'])->middleware(['auth:api'])->name('users.update');
Route::delete('/users/{user}', [UsersController::class, 'destroy'])->middleware(['auth:api'])->name('users.destroy');
Route::put('/users/changePwd/{user}', [UsersController::class, 'changePassword'])->middleware(['auth:api'])->name('users.changePwd');
Route::put('/users/changeEmail/{user}', [UsersController::class, 'changeEmail'])->middleware(['auth:api'])->name('users.changeEmail');


