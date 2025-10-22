<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WritingController;

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

// Home/Dashboard
Route::get('/', [HomeController::class, 'index'])->name('home');

// Lessons
Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

// Users
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Writing
Route::get('/writing', [WritingController::class, 'index'])->name('writing.index');
Route::get('/writing/demo', function () {
    return view('writing.demo');
})->name('writing.demo');
Route::get('/writing/lesson/{lesson}/exercise/{exercise}', [WritingController::class, 'showExercise'])->name('writing.exercise');
Route::post('/writing/submit-answer', [WritingController::class, 'submitAnswer'])->name('writing.submit');

// Vocabulary (TODO: Create VocabularyController)
Route::get('/vocabulary', function () {
    return view('vocabulary.index');
})->name('vocabulary.index');
