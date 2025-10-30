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
use App\Http\Controllers\TranslationController;


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

Route::post('/translate', [TranslationController::class, 'translate']);

Route::post('/lessons/{id}/restore', [LessonController::class, 'restore']);
Route::resource('lessons', LessonController::class);

Route::resource('exercises', ExerciseController::class);
Route::post('/exercises/{id}/restore', [ExerciseController::class, 'restore']);


Route::resource('exercise-types', ExerciseTypeController::class);
Route::post('/exercise-types/{id}/restore', [ExerciseTypeController::class, 'restore']);


Route::resource('questions', QuestionController::class);
Route::post('/questions/{id}/restore', [QuestionController::class, 'restore']);


Route::resource('attempts', AttemptController::class);
Route::post('/attempts/{id}/restore', [AttemptController::class, 'restore']);
Route::get('/questions/{id}/template-hint', [AttemptController::class, 'getTemplateHint']);


Route::resource('users', UserController::class);
Route::post('/users/{id}/restore', [UserController::class, 'restore']);

