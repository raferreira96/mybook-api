<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/alive', function() {
    return [
        'message' => 'Hello World!',
        'date_time' => date('Y-m-d H:i:s')
    ];
});

Route::any('/401', [AuthController::class, 'unauthorized'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function() {
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::put('/users', [UserController::class, 'update']);
    Route::post('/users/avatar', [UserController::class, 'avatar']);
    Route::post('/users/cover', [UserController::class, 'cover']);
    Route::get('/users/feed', [FeedController::class, 'feed']);
    Route::get('/users/{id}/feed', [FeedController::class, 'feed']);
    Route::get('/users/photos', [FeedController::class, 'photos']);
    Route::get('/users/{id}/photos', [FeedController::class, 'photos']);
    Route::post('/users/{id}/follow', [UserController::class, 'follow']);
    Route::get('/users/{id}/followers', [UserController::class, 'followers']);
    Route::get('/users', [UserController::class, 'read']);
    Route::get('/users/{id}', [UserController::class, 'read']);

    Route::get('/feed', [FeedController::class, 'read']);
    Route::post('/feed', [FeedController::class, 'create']);

    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::post('/posts/{id}/comment', [PostController::class, 'comment']);

    Route::get('/search', [SearchController::class, 'search']);
});