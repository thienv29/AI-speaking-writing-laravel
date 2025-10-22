<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LessonController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExerciseTypeController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\VocabularyController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('lessons', LessonController::class);
Route::resource('exercises', ExerciseController::class);
Route::resource('exercise-types', ExerciseTypeController::class);
Route::resource('questions', QuestionController::class);
Route::resource('attempts', AttemptController::class);
Route::resource('progress', ProgressController::class);
Route::resource('vocabularies', VocabularyController::class);
Route::resource('users', UserController::class);

