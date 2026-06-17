<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\DashboardController;

// API Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API Dashboard & Config
    Route::get('/dashboard', [DashboardController::class, 'stats']);
    Route::get('/metadata', function () {
        return response()->json([
            'categories' => config('lognity.categories'),
            'faculties' => config('lognity.faculties')
        ]);
    });

    // API Forum
    Route::get('/forums', [ForumController::class, 'index']);
    Route::get('/forums/{id}', [ForumController::class, 'show']);
    Route::post('/forums', [ForumController::class, 'store']);
    Route::post('/forums/{id}/update', [ForumController::class, 'update']);
    Route::delete('/forums/{id}', [ForumController::class, 'destroy']);
    Route::post('/forums/{id}/upvote', [ForumController::class, 'upvote']);
    Route::post('/forums/{id}/answers', [ForumController::class, 'storeAnswer']);
    Route::post('/answers/{id}/update', [ForumController::class, 'updateAnswer']);
    Route::delete('/answers/{id}', [ForumController::class, 'destroyAnswer']);

    // API E-Library
    Route::get('/library', [LibraryController::class, 'index']);
    Route::get('/library/{id}', [LibraryController::class, 'show']);

    // API User Profile
    Route::get('/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'showProfile']);
    Route::post('/user/profile', [\App\Http\Controllers\Api\UserController::class, 'updateProfile']);
    Route::post('/user/change-password', [\App\Http\Controllers\Api\UserController::class, 'changePassword']);

    // API New Features
    Route::post('/answers/{id}/accept', [ForumController::class, 'acceptAnswer']);
    Route::post('/report', [ForumController::class, 'report']);
    Route::get('/leaderboard', [\App\Http\Controllers\Api\UserController::class, 'leaderboard']);

    // Social & Chat Features
    Route::get('/users/{id}/follow-status', [\App\Http\Controllers\Api\SocialController::class, 'followStatus']);
    Route::post('/users/{id}/follow', [\App\Http\Controllers\Api\SocialController::class, 'follow']);
    Route::post('/users/{id}/unfollow', [\App\Http\Controllers\Api\SocialController::class, 'unfollow']);
    Route::get('/chats', [\App\Http\Controllers\Api\SocialController::class, 'getChats']);
    Route::get('/chats/{id}/messages', [\App\Http\Controllers\Api\SocialController::class, 'getMessages']);
    Route::post('/chats/{id}/messages', [\App\Http\Controllers\Api\SocialController::class, 'sendMessage']);
    Route::delete('/chats/{id}', [\App\Http\Controllers\Api\SocialController::class, 'deleteChat']);
});
