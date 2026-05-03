<?php

use App\Http\Controllers\Api\v1\ApiPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ApiRatingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('v1/posts', ApiPostController::class)
    ->middlewareFor(['index', 'show'], ['auth:sanctum', 'abilities:posts:read'])
    ->middlewareFor(['store'], ['auth:sanctum', 'abilities:posts:create'])
    ->middlewareFor(['update'], ['auth:sanctum', 'abilities:posts:update'])
    ->middlewareFor(['destroy'], ['auth:sanctum', 'abilities:posts:delete']);

// Routes API pour les ratings — protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('v1/posts/{post}/ratings',    [ApiRatingController::class, 'index']);
    Route::post('v1/posts/{post}/ratings',   [ApiRatingController::class, 'store'])
        ->middleware('abilities:posts:read');
    Route::delete('v1/posts/{post}/ratings', [ApiRatingController::class, 'destroy'])
        ->middleware('abilities:posts:delete');
});
