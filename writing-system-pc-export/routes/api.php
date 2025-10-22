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
use App\Http\Controllers\WritingQaExerciseController;
use App\Http\Controllers\WritingSentenceBuildingExerciseController;
use App\Http\Controllers\WritingCompleteSentenceExerciseController;
use App\Http\Controllers\WritingAttemptController;


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

// Existing routes
Route::resource('lessons', LessonController::class);
Route::resource('exercises', ExerciseController::class);
Route::resource('exercise-types', ExerciseTypeController::class);
Route::resource('questions', QuestionController::class);
Route::resource('attempts', AttemptController::class);
Route::resource('progress', ProgressController::class);
Route::resource('vocabulary', VocabularyController::class);
Route::resource('users', UserController::class);

// Writing System Routes
Route::prefix('writing')->group(function () {
    // Q&A Writing Exercises
    Route::resource('qa-exercises', WritingQaExerciseController::class);
    Route::get('qa-exercises/lesson/{lessonId}', [WritingQaExerciseController::class, 'getByLesson']);
    Route::get('qa-exercises/difficulty/{difficulty}', [WritingQaExerciseController::class, 'getByDifficulty']);
    
    // Sentence Building Writing Exercises
    Route::resource('sentence-building-exercises', WritingSentenceBuildingExerciseController::class);
    Route::get('sentence-building-exercises/lesson/{lessonId}', [WritingSentenceBuildingExerciseController::class, 'getByLesson']);
    Route::get('sentence-building-exercises/word-type/{wordType}', [WritingSentenceBuildingExerciseController::class, 'getByWordType']);
    Route::get('sentence-building-exercises/difficulty/{difficulty}', [WritingSentenceBuildingExerciseController::class, 'getByDifficulty']);
    Route::get('sentence-building-exercises/search', [WritingSentenceBuildingExerciseController::class, 'searchByWord']);
    
    // Complete Sentence Writing Exercises
    Route::resource('complete-sentence-exercises', WritingCompleteSentenceExerciseController::class);
    Route::get('complete-sentence-exercises/lesson/{lessonId}', [WritingCompleteSentenceExerciseController::class, 'getByLesson']);
    Route::get('complete-sentence-exercises/sentence-type/{sentenceType}', [WritingCompleteSentenceExerciseController::class, 'getBySentenceType']);
    Route::get('complete-sentence-exercises/difficulty/{difficulty}', [WritingCompleteSentenceExerciseController::class, 'getByDifficulty']);
    Route::get('complete-sentence-exercises/random', [WritingCompleteSentenceExerciseController::class, 'getRandom']);
    
    // Writing Attempts
    Route::resource('attempts', WritingAttemptController::class);
    Route::post('attempts/{attempt}/submit', [WritingAttemptController::class, 'submit']);
    Route::get('attempts/user/{userId}', [WritingAttemptController::class, 'getByUser']);
    Route::get('attempts/exercise', [WritingAttemptController::class, 'getByExercise']);
    Route::get('attempts/my-attempts', [WritingAttemptController::class, 'getMyAttempts']);
    Route::get('attempts/progress/{userId}', [WritingAttemptController::class, 'getProgressStats']);
});

