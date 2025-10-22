<?php

namespace App\Http\Controllers;

use App\Models\WritingAttempt;
use App\Models\WritingQaExercise;
use App\Models\WritingSentenceBuildingExercise;
use App\Models\WritingCompleteSentenceExercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class WritingAttemptController extends Controller
{
    /**
     * Display a listing of writing attempts
     */
    public function index(): JsonResponse
    {
        $attempts = WritingAttempt::with(['user', 'writingExercise'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts
        ]);
    }

    /**
     * Store a newly created writing attempt
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'writing_exercise_type' => 'required|in:writing_qa_exercise,writing_sentence_building_exercise,writing_complete_sentence_exercise',
                'writing_exercise_id' => 'required|integer',
                'user_answer' => 'required|string',
                'time_spent' => 'nullable|integer|min:0'
            ]);

            // Verify the writing exercise exists
            $exercise = $this->getWritingExercise($validated['writing_exercise_type'], $validated['writing_exercise_id']);
            if (!$exercise) {
                return response()->json([
                    'success' => false,
                    'message' => 'Writing exercise not found'
                ], 404);
            }

            // Get user's attempt number for this exercise
            $attemptNumber = WritingAttempt::where('user_id', Auth::id())
                ->where('writing_exercise_type', $validated['writing_exercise_type'])
                ->where('writing_exercise_id', $validated['writing_exercise_id'])
                ->max('attempt_number') + 1;

            $attempt = WritingAttempt::create([
                'user_id' => Auth::id(),
                'writing_exercise_type' => $validated['writing_exercise_type'],
                'writing_exercise_id' => $validated['writing_exercise_id'],
                'attempt_number' => $attemptNumber,
                'user_answer' => $validated['user_answer'],
                'time_spent' => $validated['time_spent'] ?? null,
                'started_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Writing attempt created successfully',
                'data' => $attempt->load(['user', 'writingExercise'])
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
     * Display the specified writing attempt
     */
    public function show(WritingAttempt $writingAttempt): JsonResponse
    {
        $writingAttempt->load(['user', 'writingExercise']);

        return response()->json([
            'success' => true,
            'data' => $writingAttempt
        ]);
    }

    /**
     * Update the specified writing attempt
     */
    public function update(Request $request, WritingAttempt $writingAttempt): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_answer' => 'sometimes|string',
                'time_spent' => 'nullable|integer|min:0',
                'is_submitted' => 'sometimes|boolean',
                'is_correct' => 'sometimes|boolean',
                'feedback' => 'nullable|string',
                'score' => 'nullable|numeric|min:0|max:100'
            ]);

            $writingAttempt->update($validated);

            // If submitting, update submission time
            if (isset($validated['is_submitted']) && $validated['is_submitted']) {
                $writingAttempt->submit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Writing attempt updated successfully',
                'data' => $writingAttempt->load(['user', 'writingExercise'])
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
     * Remove the specified writing attempt
     */
    public function destroy(WritingAttempt $writingAttempt): JsonResponse
    {
        $writingAttempt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Writing attempt deleted successfully'
        ]);
    }

    /**
     * Submit a writing attempt
     */
    public function submit(Request $request, WritingAttempt $writingAttempt): JsonResponse
    {
        try {
            $validated = $request->validate([
                'feedback' => 'nullable|string',
                'score' => 'nullable|numeric|min:0|max:100',
                'is_correct' => 'nullable|boolean'
            ]);

            $writingAttempt->update([
                'is_submitted' => true,
                'submitted_at' => now(),
                'feedback' => $validated['feedback'] ?? null,
                'score' => $validated['score'] ?? null,
                'is_correct' => $validated['is_correct'] ?? null
            ]);

            $writingAttempt->submit(); // This will calculate word and character counts

            return response()->json([
                'success' => true,
                'message' => 'Writing attempt submitted successfully',
                'data' => $writingAttempt->load(['user', 'writingExercise'])
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
     * Get attempts by user
     */
    public function getByUser($userId): JsonResponse
    {
        $attempts = WritingAttempt::where('user_id', $userId)
            ->with(['writingExercise'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts
        ]);
    }

    /**
     * Get attempts by writing exercise
     */
    public function getByExercise(Request $request): JsonResponse
    {
        $exerciseType = $request->get('exercise_type');
        $exerciseId = $request->get('exercise_id');

        if (!$exerciseType || !$exerciseId) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise type and ID are required'
            ], 400);
        }

        $attempts = WritingAttempt::where('writing_exercise_type', $exerciseType)
            ->where('writing_exercise_id', $exerciseId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts
        ]);
    }

    /**
     * Get current user's attempts
     */
    public function getMyAttempts(): JsonResponse
    {
        $attempts = WritingAttempt::where('user_id', Auth::id())
            ->with(['writingExercise'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts
        ]);
    }

    /**
     * Get user's progress statistics
     */
    public function getProgressStats($userId): JsonResponse
    {
        $stats = WritingAttempt::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_attempts,
                COUNT(CASE WHEN is_submitted = 1 THEN 1 END) as submitted_attempts,
                COUNT(CASE WHEN is_correct = 1 THEN 1 END) as correct_attempts,
                AVG(score) as average_score,
                AVG(word_count) as average_word_count,
                AVG(time_spent) as average_time_spent
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Helper method to get writing exercise by type and ID
     */
    private function getWritingExercise($type, $id)
    {
        switch ($type) {
            case 'writing_qa_exercise':
                return WritingQaExercise::find($id);
            case 'writing_sentence_building_exercise':
                return WritingSentenceBuildingExercise::find($id);
            case 'writing_complete_sentence_exercise':
                return WritingCompleteSentenceExercise::find($id);
            default:
                return null;
        }
    }
}