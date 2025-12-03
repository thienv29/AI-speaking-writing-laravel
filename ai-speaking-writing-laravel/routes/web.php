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
    // Redirect to new embed route based on question's exercise
    $question = \App\Models\Question::with('exercises.type')->find($id);
    if (!$question || $question->exercises->isEmpty()) {
        abort(404, 'Question not found');
    }
    $exercise = $question->exercises->first();
    $typeCode = strtoupper($exercise->type->code ?? '');
    $routeName = str_starts_with($typeCode, 'W') ? 'embed.writing' : 'embed.speaking';
    return redirect()->route($routeName, [
        'exercise' => $exercise->id,
        'questionId' => $question->id
    ]);
})->name('user.question');

Route::get('/writing/question/{id}', function ($id) {
    // Redirect to new embed route based on question's exercise
    $question = \App\Models\Question::with('exercises.type')->find($id);
    if (!$question || $question->exercises->isEmpty()) {
        abort(404, 'Question not found');
    }
    $exercise = $question->exercises->first();
    $typeCode = strtoupper($exercise->type->code ?? '');
    $routeName = str_starts_with($typeCode, 'W') ? 'embed.writing' : 'embed.speaking';
    return redirect()->route($routeName, [
        'exercise' => $exercise->id,
        'questionId' => $question->id
    ]);
})->name('writing.question');

// ============================================
// EMBED ROUTES (for iframe)
// ============================================

Route::get('/embed-writing/exercises/{exercise}', [WritingController::class, 'embedWriting'])->name('embed.writing');
Route::get('/embed-speaking/exercises/{exercise}', [WritingController::class, 'embedSpeaking'])->name('embed.speaking');

// Review Routes
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
