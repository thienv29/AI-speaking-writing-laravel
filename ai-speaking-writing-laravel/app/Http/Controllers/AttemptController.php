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
                    'question:id,order_index',
                    'question.exercises:id,lesson_id,type_id,title,instruction,difficulty,order_index',
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
            // Normalize user_id: convert to integer or null
            $userIdInput = $request->input('user_id');
            if ($userIdInput !== null && $userIdInput !== '') {
                $userIdInput = (int) $userIdInput;
                if ($userIdInput <= 0) {
                    $userIdInput = null;
                }
            } else {
                $userIdInput = null;
            }
            if ($userIdInput !== null) {
                $request->merge(['user_id' => $userIdInput]);
            }

            $validated = $request->validate([
                'user_id'       => ['nullable','integer', Rule::exists('users','id')->withoutTrashed()],
                'question_id'   => ['required','integer', Rule::exists('questions','id')],
                'user_answer'   => ['required','string','max:5000'], // Tăng từ 255 lên 5000 cho writing
                'user_audio'    => ['nullable','file','mimes:mp3,wav,m4a,ogg,webm'],
            ], [
                'user_id.integer'      => 'User ID phải là số.',
                'user_id.exists'       => 'Người dùng không tồn tại. Vui lòng kiểm tra lại user_id hoặc liên hệ admin.',
                'question_id.required' => 'Câu hỏi là bắt buộc.',
                'question_id.integer'  => 'Question ID phải là số.',
                'question_id.exists'   => 'Câu hỏi không tồn tại.',
                'user_answer.string'   => 'Câu trả lời phải là chuỗi.',
                'user_answer.max'      => 'Câu trả lời không được dài quá 5000 ký tự.',
                'user_answer.required' => 'Bạn phải nhập câu trả lời',
                'user_audio.file'      => 'Tệp audio không hợp lệ.',
                'user_audio.mimes'     => 'Tệp audio phải có định dạng mp3, wav, m4a, ogg hoặc webm.',
            ]);

            $userId      = $validated['user_id'] ?? null;
            $questionId  = $validated['question_id'];
            $userAnswer  = trim($validated['user_answer']);
            $userAudioUrl = null;

            if ($request->hasFile('user_audio')) {
                $file = $request->file('user_audio');
                $filename = uniqid('audio_') . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/user_audio_url', $filename);
                $userAudioUrl = Storage::url('user_audio_url/' . $filename);
            }

            // Xác định loại bài tập dựa trên exercise type code
            $question = \App\Models\Question::with('exercises.type')->findOrFail($questionId);
            $exercise = $question->exercises->first();
            $exerciseTypeCode = $exercise->type->code ?? '';
            
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
            Log::warning('Attempt validation error', [
                'errors' => $e->errors(),
                'request_data' => [
                    'user_id' => $request->input('user_id'),
                    'question_id' => $request->input('question_id'),
                    'user_answer_length' => strlen($request->input('user_answer') ?? ''),
                    'has_user_audio' => $request->hasFile('user_audio'),
                ]
            ]);
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
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
            'question:id,order_index',
            'question.exercises:id,lesson_id,type_id,title,instruction,difficulty,order_index']);

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
            $userId = $request->input('user_id'); // No default - can be null for guest users

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
            $userId = $request->input('user_id'); // No default - can be null for guest users // Default to user_id 2
            
            // Get all questions in this lesson (across all exercises)
            $lesson = \App\Models\Lesson::with(['exercises.questions' => function($q) {
                $q->orderBy('exercise_question.order_index');
            }])->findOrFail($lessonId);
            
            // Get all questions from all exercises in this lesson
            $allQuestions = $lesson->exercises->flatMap->questions->unique('id');
            
            // Count total questions in the lesson (all exercises combined)
            $totalQuestions = $allQuestions->count();
            $questionIds = $allQuestions->pluck('id');
            
            // Get reset timestamp if provided (for reset mode)
            $resetTimestamp = $request->input('reset_timestamp');
            
            // Get all attempts for this lesson by this user
            $attemptsQuery = Attempt::where('user_id', $userId)
                ->whereIn('question_id', $questionIds);
            
            // If reset timestamp is provided, only count attempts created AFTER reset
            if ($resetTimestamp) {
                $resetDate = date('Y-m-d H:i:s', $resetTimestamp / 1000); // Convert JS timestamp (ms) to PHP datetime
                $attemptsQuery->where('created_at', '>', $resetDate);
            }
            
            $attempts = $attemptsQuery->with('question:id,order_index')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get latest attempt for each question
            $latestAttempts = $attempts->groupBy('question_id')
                ->map(function($questionAttempts) {
                    return $questionAttempts->first(); // Get latest attempt
                });
            
            // Count how many questions have been completed (have at least one attempt)
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

    /**
     * Get exercise statistics for a user
     */
    public function getExerciseStatistics(Request $request, int $exerciseId)
    {
        try {
            $userId = $request->input('user_id'); // No default - can be null for guest users // Default to user_id 2
            
            // Get exercise with questions
            $exercise = \App\Models\Exercise::with(['questions' => function($q) {
                $q->orderBy('exercise_question.order_index');
            }])->findOrFail($exerciseId);
            
            // Get all questions for this exercise
            $questionIds = $exercise->questions->pluck('id');
            $totalQuestions = $questionIds->count();
            
            // Get reset timestamp if provided (for reset mode)
            $resetTimestamp = $request->input('reset_timestamp');
            
            // Get all attempts for this exercise by this user
            $attemptsQuery = Attempt::where('user_id', $userId)
                ->whereIn('question_id', $questionIds);
            
            // If reset timestamp is provided, only count attempts created AFTER reset
            if ($resetTimestamp) {
                $resetDate = date('Y-m-d H:i:s', $resetTimestamp / 1000); // Convert JS timestamp (ms) to PHP datetime
                $attemptsQuery->where('created_at', '>', $resetDate);
            }
            
            $attempts = $attemptsQuery->with('question:id,order_index')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get latest attempt for each question
            $latestAttempts = $attempts->groupBy('question_id')
                ->map(function($questionAttempts) {
                    return $questionAttempts->first(); // Get latest attempt
                });
            
            // Count how many questions have been completed (have at least one attempt)
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
                    'exercise_id' => $exerciseId,
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
            Log::error('Get exercise statistics error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get exercise statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all attempts for an exercise (for reset functionality)
     */
    public function deleteExerciseAttempts(Request $request, int $exerciseId)
    {
        try {
            $userId = $request->input('user_id'); // No default - can be null for guest users
            
            // Get exercise with questions
            $exercise = \App\Models\Exercise::with('questions')->findOrFail($exerciseId);
            $questionIds = $exercise->questions->pluck('id');
            
            // Delete all attempts for this exercise by this user
            $deleted = Attempt::where('user_id', $userId)
                ->whereIn('question_id', $questionIds)
                ->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => "Deleted {$deleted} attempts for exercise {$exerciseId}",
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Delete exercise attempts error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete exercise attempts: ' . $e->getMessage()
            ], 500);
        }
    }
}
