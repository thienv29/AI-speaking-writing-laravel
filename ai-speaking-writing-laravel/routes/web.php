<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WritingController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

// Writing UI Routes
Route::get('/writing', [WritingController::class, 'index'])->name('writing.index');
Route::get('/writing/lesson/{id}', [WritingController::class, 'showLesson'])->name('writing.lesson');
Route::get('/writing/lesson/{id}/exercises', [WritingController::class, 'showLessonExercises'])->name('writing.lesson.exercises');
Route::get('/writing/question/{id}', [WritingController::class, 'show'])->name('writing.question');

// Review Routes
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
