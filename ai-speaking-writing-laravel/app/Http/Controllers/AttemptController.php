<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Services\AttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AttemptController extends Controller
{
    protected AttemptService $attemptService;

    public function __construct(AttemptService $attemptService)
    {
        $this->attemptService = $attemptService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $q = Attempt::query()
                ->with([
                    'question:id,exercise_id,order_index',
                    'question.exercise:id,lesson_id,type_id,title,instruction,difficulty,order_index',
                    'user:id,name,email'
                ])
                ->orderByDesc('id');

            if ($request->filled('question_id')) {
                $q->where('question_id', (int) $request->input('question_id'));
            }
            if ($request->filled('user_id')) {
                $q->where('user_id', (int) $request->input('user_id'));
            }

            $attempts = $q->get();

            return response()->json([
                'status' => 'success',
                'data'   => $attempts,
            ]);
        } catch (\Throwable $e) {
            Log::error('Attempt index error', ['error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch attempts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'       => ['nullable','integer', Rule::exists('users','id')],
                'question_id'   => ['required','integer', Rule::exists('questions','id')],
                'user_answer'   => ['required','string','max:255'],
                'user_audio'    => ['nullable','file','mimes:mp3,wav,m4a,ogg,webm'],
            ], [
                'user_id.integer'      => 'User ID phải là số.',
                'user_id.exists'       => 'Người dùng không tồn tại.',
                'question_id.required' => 'Câu hỏi là bắt buộc.',
                'question_id.integer'  => 'Question ID phải là số.',
                'question_id.exists'   => 'Câu hỏi không tồn tại.',
                'user_answer.string'   => 'Câu trả lời phải là chuỗi.',
                'user_answer.max'      => 'Câu trả lời không được dài quá 255 ký tự.',
                'user_answer.required' => 'Bạn phải nhập câu trả lời',
                'user_audio.file'      => 'Tệp audio không hợp lệ.',
                'user_audio.mimes'     => 'Tệp audio phải có định dạng mp3, wav, m4a, ogg hoặc webm.',
            ]);

            $userId      = $validated['user_id'];
            $questionId  = $validated['question_id'];
            $userAnswer  = $validated['user_answer'];
            $userAudioUrl = null;

            if ($request->hasFile('user_audio')) {
                $file = $request->file('user_audio');
                $filename = uniqid('audio_') . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/user_audio_url', $filename);
                $userAudioUrl = Storage::url('user_audio_url/' . $filename);
            }

            // Xác định loại bài tập dựa trên exercise type code
            $question = \App\Models\Question::with('exercise.type')->findOrFail($questionId);
            $exerciseTypeCode = $question->exercise->type->code ?? '';
            
            // Writing exercises: WAQ, WCS, WSG
            // Speaking exercises: SPS, SPW
            $isWritingExercise = in_array($exerciseTypeCode, ['WAQ', 'WCS', 'WSG']);
            $isSpeakingExercise = in_array($exerciseTypeCode, ['SPS', 'SPW']);
            
            if ($isWritingExercise) {
                // Auto-evaluate writing using AttemptService
                $attempt = $this->attemptService->evaluateWritingAttempt(
                    $questionId, $userId, $userAnswer
                );
            } 
            elseif ($isSpeakingExercise) {
                // Auto-evaluate speaking
                $attempt = $this->attemptService->evaluateSpeakingAttempt(
                    $questionId, $userId, $userAnswer, $userAudioUrl
                );
            }
            else {
                // Fallback: dùng logic cũ nếu không xác định được type
                $isWritingAttempt = !empty($userAnswer) && empty($userAudioUrl);
                if ($isWritingAttempt) {
                    $attempt = $this->attemptService->evaluateWritingAttempt(
                        $questionId, $userId, $userAnswer
                    );
                } else {
                    $attempt = $this->attemptService->evaluateSpeakingAttempt(
                        $questionId, $userId, $userAnswer, $userAudioUrl
                    );
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Attempt created successfully.',
                'data'    => $attempt,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Attempt store error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot create attempt: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function show(Attempt $attempt)
    {
        try {
            $attempt->load(['user:id,name,email', 
            'question:id,exercise_id,order_index',
            'question.exercise:id,lesson_id,type_id,title,instruction,difficulty,order_index']);

            return response()->json([
                'status' => 'success',
                'data'   => $attempt,
            ]);
        } catch (\Throwable $e) {
            Log::error('Attempt show error', ['id' => $attempt->id ?? null, 'error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch attempt: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Get template hint for a question
     */
    public function getTemplateHint(Request $request, int $questionId)
    {
        try {
            $hint = $this->attemptService->getTemplateHint($questionId);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'hint' => $hint
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get template hint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete attempts for a lesson
     */
    public function deleteLessonAttempts(Request $request, int $lessonId)
    {
        try {
            $userId = $request->input('user_id', 2);

            // Just return success without actually deleting anything
            // Attempts are kept for history/learning analytics
            return response()->json([
                'status' => 'success',
                'message' => 'Lesson reset successfully (attempts preserved)',
                'deleted_count' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Reset lesson attempts error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset lesson: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get lesson statistics for a user
     */
    public function getLessonStatistics(Request $request, int $lessonId)
    {
        try {
            $userId = $request->input('user_id', 2); // Default to user_id 2
            
            // Get all questions in this lesson
            $lesson = \App\Models\Lesson::with(['questions' => function($q) {
                $q->orderBy('order_index');
            }])->findOrFail($lessonId);
            
            $totalQuestions = $lesson->questions->count();
            $questionIds = $lesson->questions->pluck('id');
            
            // Get all attempts for this lesson by this user
            $attempts = Attempt::where('user_id', $userId)
                ->whereIn('question_id', $questionIds)
                ->with('question:id,order_index')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get latest attempt for each question
            $latestAttempts = $attempts->groupBy('question_id')
                ->map(function($questionAttempts) {
                    return $questionAttempts->first(); // Get latest attempt
                });
            
            $completedQuestions = $latestAttempts->count();
            $totalScore = $latestAttempts->sum(function($attempt) {
                return $attempt->score ?? 0;
            });
            
            // Calculate average score on scale of 10
            $averageScore = $completedQuestions > 0 
                ? round(($totalScore / $completedQuestions) / 10, 1) 
                : 0;
            
            // Count correct answers (score >= 80 or is_correct = true)
            $correctCount = $latestAttempts->filter(function($attempt) {
                return ($attempt->score >= 80) || ($attempt->is_correct === true);
            })->count();
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'lesson_id' => $lessonId,
                    'total_questions' => $totalQuestions,
                    'completed_questions' => $completedQuestions,
                    'correct_count' => $correctCount,
                    'average_score' => $averageScore,
                    'total_score' => $totalScore,
                    'attempts' => $latestAttempts->values()->map(function($attempt) {
                        return [
                            'question_id' => $attempt->question_id,
                            'question_order' => $attempt->question->order_index ?? null,
                            'score' => $attempt->score,
                            'is_correct' => $attempt->is_correct,
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get lesson statistics error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get lesson statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
