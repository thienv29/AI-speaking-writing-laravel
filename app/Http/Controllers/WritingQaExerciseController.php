<?php

namespace App\Http\Controllers;

use App\Models\WritingQaExercise;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class WritingQaExerciseController extends Controller
{
    /**
     * Display a listing of Q&A writing exercises
     */
    public function index(): JsonResponse
    {
        $exercises = WritingQaExercise::with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Store a newly created Q&A writing exercise
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'exercise_id' => 'required|exists:exercises,id',
                'title' => 'required|string|max:255',
                'question' => 'required|string',
                'instructions' => 'nullable|string',
                'sample_answers' => 'nullable|array',
                'sample_answers.*' => 'string',
                'expected_word_count' => 'integer|min:1',
                'difficulty' => 'required|in:easy,medium,hard',
                'order_index' => 'integer|min:0'
            ]);

            $exercise = WritingQaExercise::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Q&A writing exercise created successfully',
                'data' => $exercise->load('exercise')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified Q&A writing exercise
     */
    public function show(WritingQaExercise $writingQaExercise): JsonResponse
    {
        $writingQaExercise->load('exercise.lesson');

        return response()->json([
            'success' => true,
            'data' => $writingQaExercise
        ]);
    }

    /**
     * Update the specified Q&A writing exercise
     */
    public function update(Request $request, WritingQaExercise $writingQaExercise): JsonResponse
    {
        try {
            $validated = $request->validate([
                'exercise_id' => 'sometimes|exists:exercises,id',
                'title' => 'sometimes|string|max:255',
                'question' => 'sometimes|string',
                'instructions' => 'nullable|string',
                'sample_answers' => 'nullable|array',
                'sample_answers.*' => 'string',
                'expected_word_count' => 'sometimes|integer|min:1',
                'difficulty' => 'sometimes|in:easy,medium,hard',
                'order_index' => 'sometimes|integer|min:0'
            ]);

            $writingQaExercise->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Q&A writing exercise updated successfully',
                'data' => $writingQaExercise->load('exercise')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified Q&A writing exercise
     */
    public function destroy(WritingQaExercise $writingQaExercise): JsonResponse
    {
        $writingQaExercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Q&A writing exercise deleted successfully'
        ]);
    }

    /**
     * Get Q&A exercises by lesson
     */
    public function getByLesson($lessonId): JsonResponse
    {
        $exercises = WritingQaExercise::whereHas('exercise', function($query) use ($lessonId) {
            $query->where('lesson_id', $lessonId);
        })->with('exercise')
        ->orderBy('order_index')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Get Q&A exercises by difficulty
     */
    public function getByDifficulty($difficulty): JsonResponse
    {
        $exercises = WritingQaExercise::where('difficulty', $difficulty)
            ->with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }
}