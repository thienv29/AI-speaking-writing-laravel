<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WritingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;

Route::get('/', function () {
    return view('pages.user.home');
})->name('user.home');

// Writing UI Routes
Route::get('/writing', [WritingController::class, 'index'])->name('writing.index');
Route::get('/lessons', [LessonController::class, 'indexWeb'])->name('user.lessons');
Route::get('/lessons/{id}', [LessonController::class, 'showWeb'])->name('user.lesson');
Route::get('/questions/{id}', [QuestionController::class, 'showWeb'])->name('user.question');

// Embed route for iframe
Route::get('/embed/question/{id}', [WritingController::class, 'embed'])->name('writing.embed');

// Review Routes
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
