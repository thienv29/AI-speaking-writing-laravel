<?php

namespace App\Http\Controllers;

use App\Models\WritingSentenceBuildingExercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class WritingSentenceBuildingExerciseController extends Controller
{
    /**
     * Display a listing of sentence building writing exercises
     */
    public function index(): JsonResponse
    {
        $exercises = WritingSentenceBuildingExercise::with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Store a newly created sentence building writing exercise
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'exercise_id' => 'required|exists:exercises,id',
                'title' => 'required|string|max:255',
                'target_word' => 'required|string|max:100',
                'word_type' => 'nullable|in:noun,verb,adjective,adverb,preposition,conjunction,interjection',
                'word_meaning' => 'nullable|string',
                'word_example' => 'nullable|string',
                'instructions' => 'nullable|string',
                'sample_sentences' => 'nullable|array',
                'sample_sentences.*' => 'string',
                'expected_sentence_count' => 'integer|min:1',
                'expected_word_count' => 'integer|min:1',
                'difficulty' => 'required|in:easy,medium,hard',
                'order_index' => 'integer|min:0'
            ]);

            $exercise = WritingSentenceBuildingExercise::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sentence building writing exercise created successfully',
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
     * Display the specified sentence building writing exercise
     */
    public function show(WritingSentenceBuildingExercise $writingSentenceBuildingExercise): JsonResponse
    {
        $writingSentenceBuildingExercise->load('exercise.lesson');

        return response()->json([
            'success' => true,
            'data' => $writingSentenceBuildingExercise
        ]);
    }

    /**
     * Update the specified sentence building writing exercise
     */
    public function update(Request $request, WritingSentenceBuildingExercise $writingSentenceBuildingExercise): JsonResponse
    {
        try {
            $validated = $request->validate([
                'exercise_id' => 'sometimes|exists:exercises,id',
                'title' => 'sometimes|string|max:255',
                'target_word' => 'sometimes|string|max:100',
                'word_type' => 'nullable|in:noun,verb,adjective,adverb,preposition,conjunction,interjection',
                'word_meaning' => 'nullable|string',
                'word_example' => 'nullable|string',
                'instructions' => 'nullable|string',
                'sample_sentences' => 'nullable|array',
                'sample_sentences.*' => 'string',
                'expected_sentence_count' => 'sometimes|integer|min:1',
                'expected_word_count' => 'sometimes|integer|min:1',
                'difficulty' => 'sometimes|in:easy,medium,hard',
                'order_index' => 'sometimes|integer|min:0'
            ]);

            $writingSentenceBuildingExercise->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sentence building writing exercise updated successfully',
                'data' => $writingSentenceBuildingExercise->load('exercise')
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
     * Remove the specified sentence building writing exercise
     */
    public function destroy(WritingSentenceBuildingExercise $writingSentenceBuildingExercise): JsonResponse
    {
        $writingSentenceBuildingExercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sentence building writing exercise deleted successfully'
        ]);
    }

    /**
     * Get sentence building exercises by lesson
     */
    public function getByLesson($lessonId): JsonResponse
    {
        $exercises = WritingSentenceBuildingExercise::whereHas('exercise', function($query) use ($lessonId) {
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
     * Get sentence building exercises by word type
     */
    public function getByWordType($wordType): JsonResponse
    {
        $exercises = WritingSentenceBuildingExercise::where('word_type', $wordType)
            ->with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Get sentence building exercises by difficulty
     */
    public function getByDifficulty($difficulty): JsonResponse
    {
        $exercises = WritingSentenceBuildingExercise::where('difficulty', $difficulty)
            ->with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * Search exercises by target word
     */
    public function searchByWord(Request $request): JsonResponse
    {
        $word = $request->get('word');
        
        if (!$word) {
            return response()->json([
                'success' => false,
                'message' => 'Word parameter is required'
            ], 400);
        }

        $exercises = WritingSentenceBuildingExercise::where('target_word', 'like', "%{$word}%")
            ->with('exercise.lesson')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }
}