<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\TaxonomyController;
use App\Http\Controllers\Api\V1\AuthController;

Route::post('login', [AuthController::class, 'login']);

Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::middleware('role:admin|editor')->group(function () {
        Route::apiResource('posts', PostController::class);
    });

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('taxonomies', TaxonomyController::class);
    });
});

Route::get('taxonomies', [TaxonomyController::class, 'index']);
Route::get('taxonomies/{taxonomy}', [TaxonomyController::class, 'show']);
Route::get('taxonomies/tree', [TaxonomyController::class, 'tree']);