<?php

use Happytodev\BlogrComments\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'throttle.comments:comments'])->group(function () {
    Route::get('/comments/{postSlug}', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{postSlug}', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::post('/comments/{comment}/vote', [CommentController::class, 'vote'])->name('comments.vote')
        ->middleware('throttle.comments:votes');
});
