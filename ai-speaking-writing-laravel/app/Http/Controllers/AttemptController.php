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

            // Check if this is a writing attempt (has user_answer but no user_audio_url)
            $isWritingAttempt = !empty($userAnswer) && empty($userAudioUrl);
            
            if ($isWritingAttempt) {
                // Auto-evaluate writing using AttemptService
                $attempt = $this->attemptService->evaluateWritingAttempt(
                    $questionId, $userId, $userAnswer
                );
            } 
            else {
                $attempt = $this->attemptService->evaluateSpeakingAttempt(
                    $questionId, $userId, $userAnswer, $userAudioUrl
                );
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
}
