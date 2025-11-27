<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ExerciseTypeController;
use App\Http\Controllers\Admin\AttemptController;
use App\Http\Controllers\Admin\GroupController;

// Admin routes - no authentication for now
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Lessons
    Route::resource('lessons', LessonController::class);
    
    // Exercises
    Route::get('exercises/import', [ExerciseController::class, 'importExcelView'])
    ->name('exercises.import');
    Route::post('exercises/import', [ExerciseController::class, 'importExcel'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('exercises.import.post');
    Route::resource('exercises', ExerciseController::class);
    
    // Questions
    Route::resource('questions', QuestionController::class);
    Route::post('questions/bulk-assign-group', [QuestionController::class, 'bulkAssignGroup'])->name('questions.bulk-assign-group');
    Route::post('questions/bulk-delete', [QuestionController::class, 'bulkDelete'])->name('questions.bulk-delete');
    
    // Exercise Types (optional - for reference)
    Route::get('exercise-types', [ExerciseTypeController::class, 'index'])->name('exercise-types.index');
    
    // Attempts
    Route::resource('attempts', AttemptController::class)->only(['index', 'show', 'destroy']);

    // Groups
    Route::resource('groups', GroupController::class);
    Route::post('groups/{group}/add-questions', [GroupController::class, 'addQuestions'])->name('groups.add-questions');
    Route::delete('groups/{group}/questions/{questionId}', [GroupController::class, 'removeQuestion'])->name('groups.remove-question');
    Route::get('questions/search', [QuestionController::class, 'search'])->name('questions.search');
});
