<?php

use Happytodev\BlogrComments\Http\Controllers\AdminCommentController;
use Happytodev\BlogrComments\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::post('/comments/preview', [CommentController::class, 'preview'])->name('comments.preview');

    Route::get('/comments/{postSlug}', [CommentController::class, 'index'])->name('comments.index');

    Route::post('/comments/{postSlug}', [CommentController::class, 'store'])
        ->middleware('throttle.comments:comments')
        ->name('comments.store');

    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])
        ->middleware('throttle.comments:comments')
        ->name('comments.reply');

    Route::post('/comments/{comment}/vote', [CommentController::class, 'vote'])
        ->middleware('throttle.comments:votes')
        ->name('comments.vote');
});

Route::get('/admin/comments/{comment}/moderate/{action}', [AdminCommentController::class, 'moderate'])
    ->middleware(['web', 'auth', 'signed'])
    ->name('admin.comments.moderate');
