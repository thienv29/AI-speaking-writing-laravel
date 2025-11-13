<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Exercise;
use App\Services\TemplateValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    protected TemplateValidatorService $templateValidator;

    public function __construct(TemplateValidatorService $templateValidator)
    {
        $this->templateValidator = $templateValidator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Question::with([
            'exercise:id,lesson_id,type_id,title,instruction,difficulty,order_index', 
            'attempts',
        ])
        ->withCount('attempts')
        ->orderBy('exercise_id')
        ->orderBy('order_index');
        
        if ($request->has('exercise_id')) {
            $query->where('exercise_id', $request->exercise_id)
            ->orderBy('order_index'); 
        }

        $questions = $query->get();
        
        return response()->json($questions);
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
            $validated = $request->validate(
                [
                    'exercise_id'  => ['required', 'integer', Rule::exists('exercises', 'id')],
                    'img_url'      => ['nullable', 'url', 'max:2048'],
                    'audio_url'    => ['nullable', 'url', 'max:2048'],
                    'order_index'  => ['required', 'integer', 'min:1'],
                    'prompt_text'  => ['nullable', 'string'],
                    'target_text'  => ['nullable', 'string'],
                    'starter_text' => ['nullable', 'string']
                ],
                [
                    'exercise_id.required' => 'The exercise_id is required.',
                    'exercise_id.exists'   => 'The selected exercise is invalid.',

                    'img_url.url'          => 'The image URL must be valid.',
                    'audio_url.url'        => 'The audio URL must be valid.',

                    'order_index.required' => 'The order index is required.',
                    'order_index.integer'  => 'The order index must be an integer.',
                    'order_index.min'      => 'The order index must be at least 1.',

                    'prompt_text.string'   => 'The prompt text must be a string.',
                    'target_text.string'   => 'The target text must be a string.',
                    'starter_text.string'  => 'The starter text must be a string.',
                ]
            );

            foreach (['prompt_text','target_text','starter_text','img_url','audio_url'] as $field) {
                if (isset($validated[$field])) {
                    $validated[$field] = trim($validated[$field]) === '' ? null : trim($validated[$field]);
                }
            }

            $question = Question::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Question created successfully.',
                'data'    => $question->load('exercise:id,title'),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Question store error', ['error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot create question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function show(Question $question)
    {
        try {
            $question->load([
                'exercise' => function ($query) {
                    $query->with(['type:id,code,name', 'lesson:id,title'])
                          ->select('id', 'lesson_id', 'type_id', 'title', 'instruction', 'difficulty', 'order_index');
                },
            ])->loadCount('attempts');
            $question->exercise->loadCount('questions');

            $lessonId = $question->exercise->lesson_id;
            $exerciseOrder = $question->exercise->order_index;

            // ===== Previous question =====
            // 1. Trong cùng exercise
            $previous = Question::where('exercise_id', $question->exercise_id)
                ->where('order_index', '<', $question->order_index)
                ->orderBy('order_index', 'desc')
                ->first();

            // 2. Nếu không có, lấy question cuối của exercise trước trong lesson
            if (!$previous) {
                $prevExercise = Exercise::where('lesson_id', $lessonId)
                    ->where('order_index', '<', $exerciseOrder)
                    ->orderBy('order_index', 'desc')
                    ->first();

                if ($prevExercise) {
                    $previous = Question::where('exercise_id', $prevExercise->id)
                        ->orderBy('order_index', 'desc')
                        ->first();
                }
            }

            // ===== Next question =====
            // 1. Trong cùng exercise
            $next = Question::where('exercise_id', $question->exercise_id)
                ->where('order_index', '>', $question->order_index)
                ->orderBy('order_index', 'asc')
                ->first();

            //2. Nếu không có, lấy question đầu của exercise tiếp theo trong lesson
            if (!$next) {
                $nextExercise = Exercise::where('lesson_id', $lessonId)
                    ->where('order_index', '>', $exerciseOrder)
                    ->orderBy('order_index', 'asc')
                    ->first();

                if ($nextExercise) {
                    $next = Question::where('exercise_id', $nextExercise->id)
                        ->orderBy('order_index', 'asc')
                        ->first();
                }
            }

            $question->prev_question_id = $previous ? $previous->id : null;
            $question->next_question_id = $next ? $next->id : null;

            $allQuestions = Question::where('exercise_id', $question->exercise_id)
                ->orderBy('order_index')
                ->get(['id', 'order_index']);

            $templateHint = $this->templateValidator->getTemplateHint($question);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'question'       => $question,
                    'all_questions'  => $allQuestions,
                    'template_hint'  => $templateHint,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Question show error', ['error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch question: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showWeb($id)
    {
        try {
            $question = Question::findOrFail($id);

            // Load tất cả dữ liệu cần thiết
            $question->load([
                'attempts', // danh sách các attempts của question
                'exercise' => function ($query) {
                    $query->select('id', 'lesson_id', 'type_id', 'title', 'instruction', 'difficulty', 'order_index')
                        ->with([
                            'questions:id,exercise_id,order_index', 
                            'lesson:id,title,description,level',
                            'type:id,name,code'
                        ])
                        ->withCount('questions'); 
                },
            ])->loadCount('attempts'); 

            $lessonId = $question->exercise->lesson_id;
            $exerciseOrder = $question->exercise->order_index;

            // ===== Previous question =====
            // 1. Trong cùng exercise
            $previous = Question::where('exercise_id', $question->exercise_id)
                ->where('order_index', '<', $question->order_index)
                ->orderBy('order_index', 'desc')
                ->first();

            // 2. Nếu không có, lấy question cuối của exercise trước trong lesson
            if (!$previous) {
                $prevExercise = Exercise::where('lesson_id', $lessonId)
                    ->where('order_index', '<', $exerciseOrder)
                    ->orderBy('order_index', 'desc')
                    ->first();

                if ($prevExercise) {
                    $previous = Question::where('exercise_id', $prevExercise->id)
                        ->orderBy('order_index', 'desc')
                        ->first();
                }
            }

            // ===== Next question =====
            // 1. Trong cùng exercise
            $next = Question::where('exercise_id', $question->exercise_id)
                ->where('order_index', '>', $question->order_index)
                ->orderBy('order_index', 'asc')
                ->first();

            // 2. Nếu không có, lấy question đầu của exercise tiếp theo trong lesson
            if (!$next) {
                $nextExercise = Exercise::where('lesson_id', $lessonId)
                    ->where('order_index', '>', $exerciseOrder)
                    ->orderBy('order_index', 'asc')
                    ->first();

                if ($nextExercise) {
                    $next = Question::where('exercise_id', $nextExercise->id)
                        ->orderBy('order_index', 'asc')
                        ->first();
                }
            }

            $question->prev_question_id = $previous ? $previous->id : null;
            $question->next_question_id = $next ? $next->id : null;

            return view('pages.user.question',compact('question'));
        } catch (\Throwable $e) {
            Log::error('Question show error', ['error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Question $question)
    {
        try {
            $input = $request->all();

            foreach (['prompt_text','target_text','starter_text','img_url','audio_url'] as $field) {
                if (array_key_exists($field, $input) && $input[$field] === '') {
                    $input[$field] = null;
                } elseif (array_key_exists($field, $input) && !is_null($input[$field])) {
                    $input[$field] = trim((string) $input[$field]);
                }
            }

            $validated = validator(
                $input,
                [
                    'exercise_id'  => ['sometimes','required','integer', Rule::exists('exercises', 'id')],
                    'img_url'      => ['sometimes','nullable','url','max:2048'],
                    'audio_url'    => ['sometimes','nullable','url','max:2048'],
                    'order_index'  => ['sometimes','required','integer','min:1'],
                    'prompt_text'  => ['sometimes','nullable','string'],
                    'target_text'  => ['sometimes','nullable','string'],
                    'starter_text' => ['sometimes','nullable','string'],
                ],
                [
                    'exercise_id.required' => 'The exercise_id is required.',
                    'exercise_id.exists'   => 'The selected exercise is invalid.',

                    'img_url.url'          => 'The image URL must be valid.',
                    'audio_url.url'        => 'The audio URL must be valid.',

                    'order_index.required' => 'The order index is required.',
                    'order_index.integer'  => 'The order index must be an integer.',
                    'order_index.min'      => 'The order index must be at least 1.',

                    'prompt_text.string'   => 'The prompt text must be a string.',
                    'target_text.string'   => 'The target text must be a string.',
                    'starter_text.string'  => 'The starter text must be a string.',
                ]
            )->validate();

            $question->fill($validated)->save();

            $question->load('exercise:id,title')->loadCount('attempts');

            return response()->json([
                'status' => 'success',
                'data'   => $question,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Question update error', ['id' => $question->id ?? null, 'error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot update question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function destroy(Question $question)
    {
        try {
            if ($question->attempts()->exists()) {
                $question->delete();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Question has related attempts → soft deleted (marked deleted_at).',
                ]);
            }

            $question->forceDelete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Question had no related attempts → permanently deleted.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Question destroy error', [
                'id' => $question->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param string $id
     */
    public function restore(string $id)
    {
        try {
            $question = Question::withTrashed()->findOrFail($id);

            if ($question->trashed()) {
                $question->restore();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Question restored successfully.',
                ]);
            }

            return response()->json([
                'status'  => 'info',
                'message' => 'Question is already active (not deleted).',
            ]);
        } catch (\Throwable $e) {
            Log::error('Question restore error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot restore question: ' . $e->getMessage(),
            ], 500);
        }
    }
}
