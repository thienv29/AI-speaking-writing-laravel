<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\User;
use App\Models\Question;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\ExerciseType;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attempt::with(['user', 'question.exercise.type', 'question.exercise.lesson']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by question
        if ($request->filled('question_id')) {
            $query->where('question_id', $request->question_id);
        }

        // Filter by exercise
        if ($request->filled('exercise_id')) {
            $query->whereHas('question', function ($q) use ($request) {
                $q->where('exercise_id', $request->exercise_id);
            });
        }

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->whereHas('question.exercise', function ($q) use ($request) {
                $q->where('lesson_id', $request->lesson_id);
            });
        }

        // Filter by exercise type
        if ($request->filled('type_id')) {
            $query->whereHas('question.exercise', function ($q) use ($request) {
                $q->where('type_id', $request->type_id);
            });
        }

        // Filter by is_correct
        if ($request->filled('is_correct')) {
            $isCorrect = $request->is_correct === '1' || $request->is_correct === 'true';
            $query->where('is_correct', $isCorrect);
        }

        // Filter by score range
        if ($request->filled('score_min')) {
            $query->where('score', '>=', $request->score_min);
        }
        if ($request->filled('score_max')) {
            $query->where('score', '<=', $request->score_max);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by user answer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_answer', 'like', "%{$search}%")
                  ->orWhere('feedback', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $attempts = $query->paginate(20)->withQueryString();

        // Get filter options
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $questions = Question::with('exercise.type', 'exercise.lesson')
            ->orderBy('id')
            ->get(['id', 'exercise_id', 'prompt_text']);
        $exercises = Exercise::with('type', 'lesson')
            ->orderBy('title')
            ->get(['id', 'type_id', 'lesson_id', 'title']);
        $lessons = Lesson::orderBy('title')->get(['id', 'title']);
        $types = ExerciseType::orderBy('name')->get(['id', 'code', 'name']);

        return view('admin.attempts.index', compact(
            'attempts',
            'users',
            'questions',
            'exercises',
            'lessons',
            'types'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Attempt $attempt)
    {
        $attempt->load([
            'user',
            'question.exercise.type',
            'question.exercise.lesson'
        ]);

        return view('admin.attempts.show', compact('attempt'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attempt $attempt)
    {
        $attempt->delete();

        // Preserve query parameters
        $queryParams = request()->only(['search', 'user_id', 'question_id', 'exercise_id', 'lesson_id', 'type_id', 'is_correct', 'score_min', 'score_max', 'date_from', 'date_to', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.attempts.index', $queryParams)
            ->with('success', 'Attempt đã được xóa thành công!');
    }
}

