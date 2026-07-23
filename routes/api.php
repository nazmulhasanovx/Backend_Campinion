<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\AdminContentController;
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
        Route::get('/projects', [AdminContentController::class, 'projects']);
        Route::post('/projects', [AdminContentController::class, 'storeProject']);
        Route::get('/projects/{project:slug}', [AdminContentController::class, 'project']);
        Route::put('/projects/{project:slug}', [AdminContentController::class, 'updateProject']);
        Route::delete('/projects/{project:slug}', [AdminContentController::class, 'destroyProject']);
        Route::get('/services', [AdminContentController::class, 'services']);
        Route::post('/services', [AdminContentController::class, 'storeService']);
        Route::get('/services/{service:slug}', [AdminContentController::class, 'service']);
        Route::put('/services/{service:slug}', [AdminContentController::class, 'updateService']);
        Route::delete('/services/{service:slug}', [AdminContentController::class, 'destroyService']);
        Route::get('/blog', [AdminContentController::class, 'blog']);
        Route::post('/blog', [AdminContentController::class, 'storeBlogPost']);
        Route::get('/blog/{post:slug}', [AdminContentController::class, 'blogPost']);
        Route::put('/blog/{post:slug}', [AdminContentController::class, 'updateBlogPost']);
        Route::delete('/blog/{post:slug}', [AdminContentController::class, 'destroyBlogPost']);
        Route::get('/inquiries', [AdminContentController::class, 'inquiries']);
        Route::get('/media', [AdminContentController::class, 'media']);
    });
});
