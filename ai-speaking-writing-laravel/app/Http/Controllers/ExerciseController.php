<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            Exercise::with(['lesson:id,title', 'type:id,name'])
                ->withCount('questions')
                ->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        
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
            foreach (['title','instruction','img_url'] as $field) {
                if (array_key_exists($field, $input) && !is_null($request->input($field))) {
                    $input[$field] = trim((string) $request->input($field));
                }
            }

            $validated = validator(
                $input,
                [
                    'type_id'      => ['required', 'integer', Rule::exists('exercise_types', 'id')],
                    'lesson_id'    => ['required', 'integer', Rule::exists('lessons', 'id')],
                    'title'        => [
                        'required', 'string', 'max:255',
                        Rule::unique('exercises', 'title')->where(fn($q) =>
                            $q->where('lesson_id', $request->input('lesson_id'))
                        ),
                    ],

                    'instruction'  => ['required', 'string', 'max:255'],
                    'difficulty'   => ['required', 'string', 'max:255'],
                    'img_url'      => ['nullable', 'url', 'max:2048'],
                    'order_index'  => ['required', 'integer', 'min:1'],
                ],
                [
                    'type_id.required'  => 'The exercise type is required.',
                    'type_id.integer'   => 'The exercise type must be an integer.',
                    'type_id.exists'    => 'The selected exercise type is invalid.',

                    'lesson_id.required'=> 'The lesson is required.',
                    'lesson_id.integer' => 'The lesson must be an integer.',
                    'lesson_id.exists'  => 'The selected lesson is invalid.',

                    'title.required'    => 'The title is required.',
                    'title.string'      => 'The title must be a string.',
                    'title.max'         => 'The title may not be greater than 255 characters.',
                    'title.unique'      => 'The title has already been taken in this lesson.',

                    'instruction.string'=> 'The instruction must be a string.',
                    'instruction.max'   => 'The instruction may not be greater than 255 characters.',

                    'difficulty.required'=> 'The difficulty is required.',
                    'difficulty.string'  => 'The difficulty must be a string.',
                    'difficulty.max'     => 'The difficulty may not be greater than 255 characters.',

                    'img_url.url'       => 'The image URL must be a valid URL.',
                    'img_url.max'       => 'The image URL may not be greater than 2048 characters.',

                    'order_index.required'=> 'The order index is required.',
                    'order_index.integer'=> 'The order index must be an integer.',
                    'order_index.min'    => 'The order index must be at least 1.'
                ]
            )->validate();

            $exercise = Exercise::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Exercise created successfully.',
                'data'    => $exercise->load(['lesson:id,title', 'type:id,name'])->loadCount('questions'),
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Exercise store error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot create exercise: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function show(Exercise $exercise)
    {
        try {
            $exercise->load([
                'lesson:id,title',
                'type:id,name,code',
                'questions' => function ($questions) {
                    $questions->select('id','exercise_id','order_index','prompt_text','target_text','starter_text','img_url','audio_url','active')
                      ->orderBy('order_index');
                }
            ])->loadCount('questions');

            return response()->json([
                'status' => 'success',
                'data'   => $exercise,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exercise show error', ['id' => $exercise->id ?? null, 'error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch exercise: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function edit(Exercise $exercise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Exercise $exercise)
    {
        try {
            $input = $request->all();
            foreach (['title','instruction','img_url', 'difficulty'] as $field) {
                if (array_key_exists($field, $input) && !is_null($request->input($field))) 
                    $input[$field] = trim((string) $request->input($field));
            }
            if ($request->has('active') && !is_null($request->input('active'))) {
                $input['active'] = (bool) $request->input('active');
            }

            $validated = validator(
                $input,
                [
                    'type_id'      => ['sometimes','required','integer', Rule::exists('exercise_types', 'id')],
                    'lesson_id'    => ['sometimes','required','integer', Rule::exists('lessons', 'id')],
                    'title'        => [
                        'sometimes', 'required', 'string', 'max:255',
                        // unique trong lesson hiện hành (nếu có gửi lesson_id thì dùng lesson_id mới)
                        Rule::unique('exercises', 'title')
                            ->ignore($exercise->id)
                            ->where(function ($q) use ($request, $exercise) {
                                $lessonId = $request->input('lesson_id', $exercise->lesson_id);
                                return $q->where('lesson_id', $lessonId);
                            }),
                    ],
                    'instruction'  => ['sometimes','required','string','max:255'],
                    'difficulty'   => ['sometimes','required','string','max:255'],
                    'img_url'      => ['sometimes','nullable','url','max:2048'],
                    'order_index'  => ['sometimes','required','integer','min:1'],
                    'active'       => ['sometimes','required','boolean'],
                ],
                [
                    'type_id.required'  => 'The exercise type is required.',
                    'type_id.integer'   => 'The exercise type must be an integer.',
                    'type_id.exists'    => 'The selected exercise type is invalid.',

                    'lesson_id.required'=> 'The lesson is required.',
                    'lesson_id.integer' => 'The lesson must be an integer.',
                    'lesson_id.exists'  => 'The selected lesson is invalid.',

                    'title.required'    => 'The title is required.',
                    'title.string'      => 'The title must be a string.',
                    'title.max'         => 'The title may not be greater than 255 characters.',
                    'title.unique'      => 'The title has already been taken in this lesson.',

                    'instruction.string'=> 'The instruction must be a string.',
                    'instruction.max'   => 'The instruction may not be greater than 255 characters.',

                    'difficulty.string'=> 'The difficulty must be a string.',
                    'difficulty.max'    => 'The difficulty may not be greater than 255 characters.',

                    'img_url.url'       => 'The image URL must be a valid URL.',
                    'img_url.max'       => 'The image URL may not be greater than 2048 characters.',

                    'order_index.integer'=> 'The order index must be an integer.',
                    'order_index.min'    => 'The order index must be at least 0.',

                    'active.boolean'    => 'The active field must be true or false.',
                    'active.required'   => 'The active field is required.',
                ]
            )->validate();

            $exercise->fill($validated)->save();

            $exercise->load(['lesson:id,title', 'type:id,name'])->loadCount('questions');

            return response()->json([
                'status' => 'success',
                'data'   => $exercise,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Exercise update error', ['id' => $exercise->id ?? null, 'error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot update exercise: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Exercise  $exercise
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function destroy(Exercise $exercise)
    {
        try {
            if ($exercise->questions()->exists()) {
                return response()->json([
                    'status'  => 'fail',
                    'message' => 'Cannot delete: there are questions referencing this exercise.',
                ], 409);
            }

            $exercise->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Exercise deleted.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Exercise destroy error', ['id' => $exercise->id ?? null, 'error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete exercise: ' . $e->getMessage(),
            ], 500);
        }
    }
}
