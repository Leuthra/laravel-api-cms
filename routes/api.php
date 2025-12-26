<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\TaxonomyController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);
Route::post('/verify-email/{id}/{hash}', [VerifyEmailController::class]);
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store']);

Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show']);

Route::get('posts/{post:slug}/comments', [CommentController::class, 'index']);

Route::get('settings', [SettingController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    
    Route::post('posts/{post:slug}/comments', [CommentController::class, 'store']);
    
    Route::middleware('role:admin|editor')->group(function () {
        Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('taxonomies', TaxonomyController::class)->except(['index', 'show']);
        Route::post('settings', [SettingController::class, 'update']);
    });
});

Route::get('taxonomies', [TaxonomyController::class, 'index']);
Route::get('taxonomies/{taxonomy}', [TaxonomyController::class, 'show']);
Route::get('taxonomies/tree', [TaxonomyController::class, 'tree']);