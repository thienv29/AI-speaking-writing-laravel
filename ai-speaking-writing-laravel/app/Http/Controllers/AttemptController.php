<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Services\AttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

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
                    'question.exercise:id,title',
                    'user:id,name,email'
                ])
                ->orderByDesc('id');

            if ($request->filled('question_id')) {
                $q->where('question_id', $request->integer('question_id'));
            }
            if ($request->filled('user_id')) {
                $q->where('user_id', $request->integer('user_id'));
            }

            return response()->json([
                'status' => 'success',
                'data'   => $q->get(),
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $input = $request->all();

            foreach (['user_answer','user_audio_url','feedback'] as $f) {
                if (array_key_exists($f, $input)) {
                    $input[$f] = trim((string) $input[$f]);
                    if ($input[$f] === '') $input[$f] = null;
                }
            }

            // Check if this is a writing attempt (has user_answer but no user_audio_url)
            $isWritingAttempt = !empty($input['user_answer']) && empty($input['user_audio_url']);
            
            if ($isWritingAttempt) {
                // Auto-evaluate writing using AttemptService
                $attempt = $this->attemptService->evaluateWritingAttempt(
                    (int) $input['question_id'],
                    $input['user_id'] ?? null,
                    (string) $input['user_answer']
                );
                
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Writing attempt evaluated successfully.',
                    'data'    => $attempt,
                ], 201);
            }

            // Original logic for speaking attempts (manual evaluation)
            $validated = validator(
                $input,
                [
                    'question_id' => ['required','integer', Rule::exists('questions','id')],
                    'user_id'     => ['nullable','integer', Rule::exists('users','id')],

                    'user_answer' => ['required_without:user_audio_url','nullable','string','max:255'],
                    'user_audio_url'   => ['required_without:user_answer','nullable','url','max:2048'],
                    'is_correct'  => ['required','boolean'],
                    'feedback'    => ['required','string'],
                ],
                [
                    'question_id.required' => 'The question is required.',
                    'question_id.exists'   => 'The selected question is invalid.',
                    
                    'user_id.exists'       => 'The selected user is invalid.',

                    'user_answer.string'   => 'The user answer must be a string.',
                    'user_answer.max'      => 'The user answer may not be greater than 255 characters.',

                    'user_audio_url.url'        => 'The audio URL must be a valid URL.',

                    'is_correct.required' => 'is_correct is required.',
                    'is_correct.boolean'   => 'is_correct must be true or false.',
                    
                    'feedback.required' => 'Feedback is required.',
                    'feedback.string'   => 'Feedback must be a string.',
                ]
            )->validate();

            $attempt = Attempt::create($validated)
                ->load(['question:id,exercise_id,order_index','question.exercise:id,title','user:id,name,email']);

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
            $attempt->load(['user:id,name,email', 'question:id,exercise_id,order_index']);

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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function edit(Attempt $attempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attempt $attempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attempt $attempt)
    {
        //
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
