<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WritingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LessonController;

Route::get('/', function () {
    return view('pages.user.home');
})->name('user.home');

// Writing UI Routes
Route::get('/writing', [WritingController::class, 'index'])->name('writing.index');
Route::get('/lessons', [LessonController::class, 'indexWeb'])->name('user.lessons');
Route::get('/lessons/{id}', [LessonController::class, 'showWeb'])->name('user.lesson');
Route::get('/questions/{id}', function ($id) {
    // Redirect to new embed route (which will handle the redirect internally)
    return redirect()->route('writing.embed', ['id' => $id]);
})->name('user.question');
Route::get('/writing/question/{id}', function ($id) {
    // Redirect to new embed route (which will handle the redirect internally)
    return redirect()->route('writing.embed', ['id' => $id]);
})->name('writing.question');

// Embed route for iframe
Route::get('/embed/question/{id}', [WritingController::class, 'embed'])->name('writing.embed');

// Separate embed routes for writing and speaking
Route::get('/embed-writing/exercises/{exercise}', [WritingController::class, 'embedWriting'])->name('embed.writing');
Route::get('/embed-speaking/exercises/{exercise}', [WritingController::class, 'embedSpeaking'])->name('embed.speaking');

// Review Routes
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
