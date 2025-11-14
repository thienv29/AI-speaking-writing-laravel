<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ExerciseTypeController;
use App\Http\Controllers\Admin\AttemptController;

// Admin routes - no authentication for now
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Lessons
    Route::resource('lessons', LessonController::class);
    
    // Exercises
    Route::resource('exercises', ExerciseController::class);
    
    // Questions
    Route::resource('questions', QuestionController::class);
    
    // Exercise Types (optional - for reference)
    Route::get('exercise-types', [ExerciseTypeController::class, 'index'])->name('exercise-types.index');
    
    // Attempts
    Route::resource('attempts', AttemptController::class)->only(['index', 'show', 'destroy']);
});
