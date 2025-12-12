<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Api\ImageController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public image route
Route::get('/image/{filename}/', [ImageController::class, 'show'])->where('filename', '.*');

// PROTECTED ROUTES — BUTUH TOKEN
Route::middleware('auth:api')->group(function () {

    // GET USER LOGIN
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    // NEWS CRUD
    Route::get('/news', [NewsController::class, 'index']);
    Route::post('/news', [NewsController::class, 'store']);
    Route::get('/news/{id}', [NewsController::class, 'show']);
    Route::put('/news/{id}', [NewsController::class, 'update']);
    Route::delete('/news/{id}', [NewsController::class, 'destroy']);

    // COMMENTS CRUD
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/news/{newsId}', [CommentController::class, 'listByNews']);
    Route::get('/comments/{id}', [CommentController::class, 'show']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
