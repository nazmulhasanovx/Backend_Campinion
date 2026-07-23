<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Public\InquiryController;
use App\Http\Controllers\Api\Public\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'service' => 'campanion-api',
]));

Route::get('/home', [PublicContentController::class, 'home']);
Route::get('/about', [PublicContentController::class, 'about']);
Route::get('/services', [PublicContentController::class, 'services']);
Route::get('/services/{slug}', [PublicContentController::class, 'service']);
Route::get('/projects', [PublicContentController::class, 'projects']);
Route::get('/projects/{slug}', [PublicContentController::class, 'project']);
Route::get('/blog', [PublicContentController::class, 'blog']);
Route::get('/blog/{slug}', [PublicContentController::class, 'blogPost']);
Route::get('/faqs', [PublicContentController::class, 'faqs']);
Route::get('/settings', [PublicContentController::class, 'settings']);
Route::post('/contact', [InquiryController::class, 'contact']);
Route::post('/quote-requests', [InquiryController::class, 'quote']);
Route::post('/newsletter', [InquiryController::class, 'newsletter']);

Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/dashboard', DashboardController::class);
    });
});
