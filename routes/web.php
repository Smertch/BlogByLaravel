<?php

use App\Http\Controllers\Account\IndexController;
use App\Http\Controllers\Account\UpdateController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SwaggerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public RSS for readers / IDE News panel (needs CORS; no auth cookie in cross-origin fetch)
Route::get('/feed', RssFeedController::class)->name('rss.feed');
Route::options('/feed', static fn () => response('', 204, [
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET, OPTIONS',
    'Access-Control-Allow-Headers' => '*',
]))->name('rss.feed.options');

// GET/POST /register, /login, etc. are registered by Fortify (guest middleware)
Route::middleware('auth')->group(function (): void {
    Route::get('/', [PostController::class, 'index'])->name('home');
    Route::post('/locale', LocaleController::class)->name('locale.switch');
    Route::get('/account', IndexController::class)->name('index');
    Route::post('/account/profile', [UpdateController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/password', [UpdateController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [PostController::class, 'addComment'])->name('posts.comments.store');
    Route::delete('/comments/{comment}', [PostController::class, 'deleteComment'])->name('comments.destroy');
});

Route::get('/reset-password/{token}', function (Request $request) {
    return view('auth.reset-password', ['request' => $request]);
})->middleware('web')->name('password.reset');

// Swagger UI / OpenAPI spec /
Route::get('/swagger', fn () => view('swagger'))->name('swagger');
Route::get('/swagger/spec', [SwaggerController::class, 'spec'])->name('swagger.spec');
