<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Exercise;
use App\Services\TemplateValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
                'exercises:id,lesson_id,type_id,title,instruction,difficulty,order_index',
                'exercises.lesson:id,title',
                'exercises.type:id,code,name',
                'attempts',
            ])
            ->withCount('attempts')
            ->orderByDesc('id');

        if ($request->filled('exercise_id')) {
            $query->whereHas('exercises', function ($q) use ($request) {
                $q->where('exercises.id', $request->exercise_id);
            });
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
                    'exercise_ids' => ['required', 'array', 'min:1'],
                    'exercise_ids.*' => ['integer', Rule::exists('exercises', 'id')],
                    'img_url'      => ['nullable', 'url', 'max:2048'],
                    'audio_url'    => ['nullable', 'url', 'max:2048'],
                    'order_index'  => ['nullable', 'integer', 'min:1'],
                    'prompt_text'  => ['nullable', 'string'],
                    'target_text'  => ['nullable', 'string'],
                    'starter_text' => ['nullable', 'string']
                ],
                [
                    'exercise_ids.required' => 'The exercise list is required.',
                    'exercise_ids.array'    => 'Exercise IDs must be an array.',
                    'exercise_ids.min'      => 'At least one exercise is required.',
                    'exercise_ids.*.exists' => 'One of the selected exercises is invalid.',

                    'img_url.url'          => 'The image URL must be valid.',
                    'audio_url.url'        => 'The audio URL must be valid.',

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

            $exerciseIds = $validated['exercise_ids'];
            $primaryExerciseId = $exerciseIds[0];
            $orderIndex = $validated['order_index'] ?? $this->nextOrderIndex($primaryExerciseId);

            $question = Question::create([
                'exercise_id'  => $primaryExerciseId,
                'order_index'  => $orderIndex,
                'prompt_text'  => $validated['prompt_text'] ?? null,
                'target_text'  => $validated['target_text'] ?? null,
                'starter_text' => $validated['starter_text'] ?? null,
                'img_url'      => $validated['img_url'] ?? null,
                'audio_url'    => $validated['audio_url'] ?? null,
            ]);

            $question->exercises()->sync(
                $this->buildPivotData($exerciseIds)
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Question created successfully.',
                'data'    => $question->load(['exercise:id,title', 'exercises:id,title']),
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
                'exercises' => function ($query) {
                    $query->with(['type:id,code,name', 'lesson:id,title'])
                        ->orderBy('exercise_question.order_index');
                },
            ])->loadCount('attempts');

            $primaryExercise = $this->resolvePrimaryExercise($question);
            if (!$primaryExercise) {
                abort(422, 'Question is not attached to any exercise.');
            }
            $primaryExercise->loadCount('questions');

            $lessonId = $primaryExercise->lesson_id;
            $exerciseOrder = $primaryExercise->order_index;

            // ===== Previous question =====
            // 1. Trong cùng exercise
            $previous = Question::where('exercise_id', $primaryExercise->id)
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
            $next = Question::where('exercise_id', $primaryExercise->id)
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

            $allQuestions = Question::where('exercise_id', $primaryExercise->id)
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
                'attempts',
                'exercise' => function ($query) {
                    $query->select('id', 'lesson_id', 'type_id', 'title', 'instruction', 'difficulty', 'order_index')
                        ->with([
                            'questions:id,exercise_id,order_index', 
                            'lesson:id,title,description,level',
                            'type:id,name,code'
                        ])
                        ->withCount('questions'); 
                },
                'exercises' => function ($query) {
                    $query->with(['lesson:id,title', 'type:id,name,code'])
                        ->orderBy('exercise_question.order_index');
                },
            ])->loadCount('attempts'); 

            $primaryExercise = $this->resolvePrimaryExercise($question);
            if (!$primaryExercise) {
                abort(422, 'Question is not attached to any exercise.');
            }

            $lessonId = $primaryExercise->lesson_id;
            $exerciseOrder = $primaryExercise->order_index;

            // ===== Previous question =====
            // 1. Trong cùng exercise
            $previous = Question::where('exercise_id', $primaryExercise->id)
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
            $next = Question::where('exercise_id', $primaryExercise->id)
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
                    'exercise_ids' => ['sometimes','required','array','min:1'],
                    'exercise_ids.*' => ['integer', Rule::exists('exercises', 'id')],
                    'img_url'      => ['sometimes','nullable','url','max:2048'],
                    'audio_url'    => ['sometimes','nullable','url','max:2048'],
                    'order_index'  => ['sometimes','nullable','integer','min:1'],
                    'prompt_text'  => ['sometimes','nullable','string'],
                    'target_text'  => ['sometimes','nullable','string'],
                    'starter_text' => ['sometimes','nullable','string'],
                ],
                [
                    'exercise_ids.required' => 'The exercise list is required.',
                    'exercise_ids.array'    => 'Exercise IDs must be an array.',
                    'exercise_ids.min'      => 'At least one exercise is required.',
                    'exercise_ids.*.exists' => 'One of the selected exercises is invalid.',

                    'img_url.url'          => 'The image URL must be valid.',
                    'audio_url.url'        => 'The audio URL must be valid.',

                    'order_index.integer'  => 'The order index must be an integer.',
                    'order_index.min'      => 'The order index must be at least 1.',

                    'prompt_text.string'   => 'The prompt text must be a string.',
                    'target_text.string'   => 'The target text must be a string.',
                    'starter_text.string'  => 'The starter text must be a string.',
                ]
            )->validate();

            $exerciseIds = $validated['exercise_ids'] ?? $question->exercises()->pluck('exercises.id')->toArray();
            if (empty($exerciseIds)) {
                throw ValidationException::withMessages([
                    'exercise_ids' => ['At least one exercise is required.'],
                ]);
            }

            $primaryExerciseId = $exerciseIds[0];

            $question->fill([
                'exercise_id'  => $primaryExerciseId,
                'prompt_text'  => $validated['prompt_text'] ?? $question->prompt_text,
                'target_text'  => $validated['target_text'] ?? $question->target_text,
                'starter_text' => $validated['starter_text'] ?? $question->starter_text,
                'img_url'      => array_key_exists('img_url', $validated) ? $validated['img_url'] : $question->img_url,
                'audio_url'    => array_key_exists('audio_url', $validated) ? $validated['audio_url'] : $question->audio_url,
                'order_index'  => $validated['order_index'] ?? $question->order_index,
            ])->save();

            $pivotData = $this->buildPivotData($exerciseIds, $question);
            $question->exercises()->sync($pivotData);

            if (isset($validated['order_index'])) {
                $question->exercises()->updateExistingPivot($primaryExerciseId, [
                    'order_index' => $question->order_index,
                ]);
            }

            $question->load(['exercise:id,title', 'exercises:id,title'])->loadCount('attempts');

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

            $question->exercises()->detach();
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

    /**
     * Determine the next order index for a question inside an exercise.
     */
    protected function nextOrderIndex(int $exerciseId): int
    {
        $maxOrder = DB::table('exercise_question')
            ->where('exercise_id', $exerciseId)
            ->max('order_index');

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Build pivot payload for syncing exercises to a question.
     */
    protected function buildPivotData(array $exerciseIds, ?Question $question = null): array
    {
        $pivotData = [];
        $existingOrders = collect();

        if ($question) {
            $question->loadMissing('exercises');
            $existingOrders = $question->exercises->pluck('pivot.order_index', 'id');
        }

        foreach ($exerciseIds as $exerciseId) {
            if ($existingOrders->has($exerciseId)) {
                $pivotData[$exerciseId] = [
                    'order_index' => $existingOrders[$exerciseId],
                ];
                continue;
            }

            $maxOrder = DB::table('exercise_question')
                ->where('exercise_id', $exerciseId)
                ->when($question, function ($query) use ($question) {
                    $query->where('question_id', '<>', $question->id);
                })
                ->max('order_index');

            $pivotData[$exerciseId] = [
                'order_index' => ($maxOrder ?? 0) + 1,
            ];
        }

        return $pivotData;
    }

    /**
     * Resolve the primary exercise for a question.
     */
    protected function resolvePrimaryExercise(Question $question): ?Exercise
    {
        if ($question->relationLoaded('exercise') && $question->exercise) {
            return $question->exercise;
        }

        if ($question->exercise_id) {
            return Exercise::find($question->exercise_id);
        }

        if ($question->relationLoaded('exercises')) {
            return $question->exercises->first();
        }

        return $question->exercises()->first();
    }
}
