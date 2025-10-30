<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Exercise;
use App\Models\Question;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display review page with attempts
     */
    public function index(Request $request)
    {
        $query = Attempt::with([
            'user:id,name,email',
            'question:id,exercise_id,prompt_text,order_index',
            'question.exercise:id,lesson_id,title,type_id',
            'question.exercise.lesson:id,title,level',
            'question.exercise.type:id,name,code'
        ])
        ->orderByDesc('created_at');

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        // Handle lesson_id or exercise_id from combined select
        $lessonExerciseFilter = $request->input('lesson_id');
        if ($lessonExerciseFilter) {
            if (str_starts_with($lessonExerciseFilter, 'lesson_')) {
                $lessonId = str_replace('lesson_', '', $lessonExerciseFilter);
                $query->whereHas('question.exercise', function($q) use ($lessonId) {
                    $q->where('lesson_id', $lessonId);
                });
            } elseif (str_starts_with($lessonExerciseFilter, 'exercise_')) {
                $exerciseId = str_replace('exercise_', '', $lessonExerciseFilter);
                $query->whereHas('question', function($q) use ($exerciseId) {
                    $q->where('exercise_id', $exerciseId);
                });
            }
        }

        if ($request->filled('question_id')) {
            $query->where('question_id', $request->integer('question_id'));
        }

        if ($request->filled('is_correct')) {
            $query->where('is_correct', $request->boolean('is_correct'));
        }

        $attempts = $query->paginate(20);

        // Get filter options - Group exercises by lesson
        $users = User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']);
        $lessons = Lesson::with(['exercises' => function($q) {
            $q->whereHas('questions')
              ->with('type:id,name,code')
              ->orderBy('order_index');
        }])
        ->whereHas('exercises.questions')
        ->orderBy('level')
        ->orderBy('title')
        ->get(['id', 'title', 'level']);

        return view('review.index', compact('attempts', 'users', 'lessons'));
    }
}

