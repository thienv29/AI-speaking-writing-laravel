<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Exercise;
use App\Models\Question;
use App\Models\Attempt;
use App\Models\User;
use App\Models\ExerciseType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $stats = [
            'lessons' => Lesson::count(),
            'exercises' => Exercise::count(),
            'questions' => Question::count(),
            'users' => User::count(),
            'attempts' => Attempt::count(),
        ];

        // Attempt statistics
        $totalAttempts = Attempt::count();
        $correctAttempts = Attempt::where('is_correct', true)->count();
        $incorrectAttempts = Attempt::where('is_correct', false)->count();
        $correctRate = $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100, 1) : 0;
        
        // Average score (only for writing attempts with score)
        $avgScore = Attempt::whereNotNull('score')->avg('score');
        $avgScore = $avgScore ? round($avgScore, 1) : 0;

        // Attempts by type
        $attemptsByType = Attempt::with(['question.exercise.type', 'question.exercises.type'])
            ->get()
            ->groupBy(function ($attempt) {
                $exercise = $attempt->question->exercise ?? $attempt->question->exercises->first();
                return $exercise->type->code ?? 'UNKNOWN';
            })
            ->map(function ($group) {
                $exercise = $group->first()->question->exercise ?? $group->first()->question->exercises->first();
                return [
                    'code' => $exercise->type->code ?? 'UNKNOWN',
                    'name' => $exercise->type->name ?? 'Unknown',
                    'count' => $group->count(),
                    'correct' => $group->where('is_correct', true)->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        // Recent attempts (last 10)
        $recentAttempts = Attempt::with([
            'user:id,name',
            'question:id,exercise_id,prompt_text',
            'question.exercise:id,title,type_id',
            'question.exercise.type:id,code,name',
            'question.exercises:id,title'
        ])
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

        // Attempts by time period
        $todayAttempts = Attempt::whereDate('created_at', Carbon::today())->count();
        $weekAttempts = Attempt::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $monthAttempts = Attempt::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        // Exercises without questions
        $exercisesWithoutQuestions = Exercise::doesntHave('questions')->count();
        
        // Questions without img_url (for speaking)
        $speakingQuestionsWithoutImg = Question::whereNull('img_url')
            ->where(function ($query) {
                $query->whereHas('exercise.type', function($q) {
                    $q->whereIn('code', ['SPS', 'SPW']);
                })->orWhereHas('exercises.type', function($q) {
                    $q->whereIn('code', ['SPS', 'SPW']);
                });
            })
            ->count();

        return view('admin.dashboard', compact(
            'stats',
            'correctRate',
            'correctAttempts',
            'incorrectAttempts',
            'avgScore',
            'attemptsByType',
            'recentAttempts',
            'todayAttempts',
            'weekAttempts',
            'monthAttempts',
            'exercisesWithoutQuestions',
            'speakingQuestionsWithoutImg'
        ));
    }
}
