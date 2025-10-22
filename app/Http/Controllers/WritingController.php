<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Exercise;
use App\Models\Question;
use App\Models\ExerciseType;
use App\Models\Attempt;
use App\Services\WritingScoringService;
use App\Services\AdvancedWritingScoringService;
use App\Services\PlagiarismDetectionService;
use App\Services\GrammarAnalysisService;
use Illuminate\Http\Request;

class WritingController extends Controller
{
    public function index()
    {
        // Get all writing exercise types
        $writingTypes = ExerciseType::where('name', 'like', '%writing%')
            ->orWhere('name', 'like', '%Writing%')
            ->get();
            
        // Get lessons with writing exercises
        $lessons = Lesson::with(['exercises' => function($query) use ($writingTypes) {
            $query->whereIn('type_id', $writingTypes->pluck('id'));
        }])->get();
        
        return view('writing.index', compact('lessons', 'writingTypes'));
    }

    public function showExercise($lessonId, $exerciseId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $exercise = Exercise::with(['questions', 'type'])->findOrFail($exerciseId);
        
        return view('writing.exercise', compact('lesson', 'exercise'));
    }

    public function submitAnswer(Request $request)
    {
        $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'scoring_type' => 'required|in:auto,manual,advanced_ai'
        ]);

        $exercise = Exercise::find($request->exercise_id);
        $question = Question::find($request->question_id);
        $userId = $request->user_id;
        $scoringType = $request->scoring_type;

                if ($scoringType === 'auto') {
                    // Auto-scoring using Writing Scoring Service
                    $scoringService = new WritingScoringService();
                    $result = $scoringService->scoreAnswer($exercise, $question, $request->answer);
            
            // Save attempt with auto score
            $attempt = Attempt::create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'attempt_number' => 1,
                'user_answer' => $request->answer,
                'is_correct' => $result['score'] >= 70 ? 1 : 0,
                'feedback' => implode('; ', $result['feedback']),
                'score' => $result['score'],
                'scoring_type' => 'auto'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Answer submitted and scored automatically!',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'score' => $result['score'],
                    'feedback' => $result['feedback'],
                    'scoring_type' => 'auto'
                ]
            ]);
            
        } elseif ($scoringType === 'advanced_ai') {
            // Advanced AI-powered scoring
            $advancedScoringService = new AdvancedWritingScoringService();
            $plagiarismService = new PlagiarismDetectionService();
            $grammarService = new GrammarAnalysisService();
            
            // Get comprehensive analysis
            $scoringResult = $advancedScoringService->scoreAnswer($exercise, $question, $request->answer);
            $plagiarismResult = $plagiarismService->detectPlagiarism($request->answer, $userId);
            $grammarResult = $grammarService->analyzeGrammar($request->answer);
            
            // Combine all results
            $combinedFeedback = array_merge(
                $scoringResult['feedback'] ?? [],
                $plagiarismResult['recommendations'] ?? [],
                $grammarResult['suggestions'] ?? []
            );
            
            $combinedSuggestions = array_merge(
                $scoringResult['suggestions'] ?? [],
                $grammarResult['suggestions'] ?? []
            );
            
            // Save attempt with advanced AI score
            $attempt = Attempt::create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'attempt_number' => 1,
                'user_answer' => $request->answer,
                'is_correct' => $scoringResult['score'] >= 70 ? 1 : 0,
                'feedback' => implode('; ', $combinedFeedback),
                'score' => $scoringResult['score'],
                'scoring_type' => 'advanced_ai'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Answer submitted and scored with advanced AI analysis!',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'score' => $scoringResult['score'],
                    'feedback' => $combinedFeedback,
                    'suggestions' => $combinedSuggestions,
                    'scoring_breakdown' => $scoringResult['scoring_breakdown'] ?? [],
                    'plagiarism_check' => $plagiarismResult,
                    'grammar_analysis' => $grammarResult,
                    'scoring_type' => 'advanced_ai'
                ]
            ]);
            
        } else {
            // Manual scoring - submit for teacher review
            $attempt = Attempt::create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'attempt_number' => 1,
                'user_answer' => $request->answer,
                'is_correct' => null, // Pending teacher review
                'feedback' => 'Pending teacher review',
                'score' => null, // Pending teacher review
                'scoring_type' => 'manual'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Answer submitted for teacher review!',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'scoring_type' => 'manual',
                    'status' => 'pending_review'
                ]
            ]);
        }
    }

    /**
     * Advanced AI scoring endpoint
     */
    public function advancedScoring(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'exercise_id' => 'required|exists:exercises,id',
            'question_id' => 'required|exists:questions,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $exercise = Exercise::find($request->exercise_id);
        $question = Question::find($request->question_id);
        
        $advancedScoringService = new AdvancedWritingScoringService();
        $plagiarismService = new PlagiarismDetectionService();
        $grammarService = new GrammarAnalysisService();
        
        // Get comprehensive analysis
        $scoringResult = $advancedScoringService->scoreAnswer($exercise, $question, $request->text);
        $plagiarismResult = $plagiarismService->detectPlagiarism($request->text, $request->user_id);
        $grammarResult = $grammarService->analyzeGrammar($request->text);
        
        return response()->json([
            'success' => true,
            'data' => [
                'scoring' => $scoringResult,
                'plagiarism' => $plagiarismResult,
                'grammar' => $grammarResult,
                'timestamp' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Plagiarism check endpoint
     */
    public function checkPlagiarism(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $plagiarismService = new PlagiarismDetectionService();
        $result = $plagiarismService->detectPlagiarism($request->text, $request->user_id);
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Grammar analysis endpoint
     */
    public function analyzeGrammar(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $grammarService = new GrammarAnalysisService();
        $result = $grammarService->analyzeGrammar($request->text);
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Get grammar improvement tips
     */
    public function getGrammarTips()
    {
        $grammarService = new GrammarAnalysisService();
        $tips = $grammarService->getGrammarImprovementTips();
        
        return response()->json([
            'success' => true,
            'data' => $tips
        ]);
    }
}
