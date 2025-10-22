<?php

namespace App\Http\Controllers;

use App\Models\WritingCompleteSentenceExercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class WritingCompleteSentenceExerciseController extends Controller
{
    /**
     * Display a listing of complete sentence writing exercises
     */
    public function index(): JsonResponse
    {
        $exercises = WritingCompleteSentenceExercise::with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Store a newly created complete sentence writing exercise
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'exercise_id' => 'required|exists:exercises,id',
                'title' => 'required|string|max:255',
                'sentence_start' => 'required|string',
                'instructions' => 'nullable|string',
                'hint_words' => 'nullable|array',
                'hint_words.*' => 'string',
                'sample_completions' => 'nullable|array',
                'sample_completions.*' => 'string',
                'expected_word_count' => 'integer|min:1',
                'sentence_type' => 'required|in:simple,compound,complex',
                'difficulty' => 'required|in:easy,medium,hard',
                'order_index' => 'integer|min:0'
            ]);

            $exercise = WritingCompleteSentenceExercise::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Complete sentence writing exercise created successfully',
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
     * Display the specified complete sentence writing exercise
     */
    public function show(WritingCompleteSentenceExercise $writingCompleteSentenceExercise): JsonResponse
    {
        $writingCompleteSentenceExercise->load('exercise.lesson');

        return response()->json([
            'success' => true,
            'data' => $writingCompleteSentenceExercise
        ]);
    }

    /**
     * Update the specified complete sentence writing exercise
     */
    public function update(Request $request, WritingCompleteSentenceExercise $writingCompleteSentenceExercise): JsonResponse
    {
        try {
            $validated = $request->validate([
                'exercise_id' => 'sometimes|exists:exercises,id',
                'title' => 'sometimes|string|max:255',
                'sentence_start' => 'sometimes|string',
                'instructions' => 'nullable|string',
                'hint_words' => 'nullable|array',
                'hint_words.*' => 'string',
                'sample_completions' => 'nullable|array',
                'sample_completions.*' => 'string',
                'expected_word_count' => 'sometimes|integer|min:1',
                'sentence_type' => 'sometimes|in:simple,compound,complex',
                'difficulty' => 'sometimes|in:easy,medium,hard',
                'order_index' => 'sometimes|integer|min:0'
            ]);

            $writingCompleteSentenceExercise->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Complete sentence writing exercise updated successfully',
                'data' => $writingCompleteSentenceExercise->load('exercise')
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
     * Remove the specified complete sentence writing exercise
     */
    public function destroy(WritingCompleteSentenceExercise $writingCompleteSentenceExercise): JsonResponse
    {
        $writingCompleteSentenceExercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete sentence writing exercise deleted successfully'
        ]);
    }

    /**
     * Get complete sentence exercises by lesson
     */
    public function getByLesson($lessonId): JsonResponse
    {
        $exercises = WritingCompleteSentenceExercise::whereHas('exercise', function($query) use ($lessonId) {
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
     * Get complete sentence exercises by sentence type
     */
    public function getBySentenceType($sentenceType): JsonResponse
    {
        $exercises = WritingCompleteSentenceExercise::where('sentence_type', $sentenceType)
            ->with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Get complete sentence exercises by difficulty
     */
    public function getByDifficulty($difficulty): JsonResponse
    {
        $exercises = WritingCompleteSentenceExercise::where('difficulty', $difficulty)
            ->with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Get random exercise for practice
     */
    public function getRandom(Request $request): JsonResponse
    {
        $difficulty = $request->get('difficulty');
        $sentenceType = $request->get('sentence_type');
        
        $query = WritingCompleteSentenceExercise::with('exercise.lesson');
        
        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }
        
        if ($sentenceType) {
            $query->where('sentence_type', $sentenceType);
        }
        
        $exercise = $query->inRandomOrder()->first();

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'No exercise found with the specified criteria'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exercise
        ]);
    }
}