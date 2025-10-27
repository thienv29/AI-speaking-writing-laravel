<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Question::query()
                ->with('exercise:id,title')
                ->orderBy('exercise_id')
                ->orderBy('order_index');

            if ($request->filled('exercise_id')) {
                $query->where('exercise_id', $request->integer('exercise_id'));
            }

            return response()->json([
                'status' => 'success',
                'data'   => $query->get(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Question index error', ['error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch questions: ' . $e->getMessage(),
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
            $question->load('exercise:id,title');

            return response()->json([
                'status' => 'success',
                'data'   => $question,
            ]);
        } catch (\Throwable $e) {
            Log::error('Question show error', ['error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
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

    public function restore($id)
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
