<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Question;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdvancedWritingScoringService
{
    private $openaiApiKey;
    private $openaiBaseUrl = 'https://api.openai.com/v1';
    
    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    /**
     * Advanced AI-powered scoring with multiple criteria
     */
    public function scoreAnswer(Exercise $exercise, Question $question, string $userAnswer): array
    {
        $originalAnswer = trim($userAnswer);
        
        // Basic validation
        if (empty($originalAnswer)) {
            return $this->createResponse(0, ['Please provide an answer'], ['Try to write something related to the question']);
        }

        // Get exercise context
        $exerciseType = $exercise->type->code;
        $targetAnswer = trim($question->target_text ?? '');
        $promptText = $question->prompt_text ?? '';
        $starterText = $question->starter_text ?? '';
        $difficulty = $exercise->difficulty ?? 'medium';

        // Initialize scoring components
        $scoringResults = [
            'content_relevance' => $this->scoreContentRelevance($userAnswer, $question, $exerciseType),
            'grammar_accuracy' => $this->scoreGrammarAccuracy($userAnswer),
            'vocabulary_usage' => $this->scoreVocabularyUsage($userAnswer, $difficulty),
            'coherence_structure' => $this->scoreCoherenceStructure($userAnswer),
            'creativity_originality' => $this->scoreCreativityOriginality($userAnswer, $targetAnswer),
            'length_appropriateness' => $this->scoreLengthAppropriateness($userAnswer, $targetAnswer, $question),
            'spelling_accuracy' => $this->scoreSpellingAccuracy($userAnswer),
            'punctuation_usage' => $this->scorePunctuationUsage($userAnswer),
            'sentence_variety' => $this->scoreSentenceVariety($userAnswer),
            'task_completion' => $this->scoreTaskCompletion($userAnswer, $exerciseType, $targetAnswer, $promptText, $starterText)
        ];

        // Calculate weighted total score
        $totalScore = $this->calculateWeightedScore($scoringResults, $exerciseType);
        
        // Generate AI-powered feedback
        $feedback = $this->generateAIFeedback($userAnswer, $scoringResults, $question, $exercise);
        
        // Generate specific suggestions
        $suggestions = $this->generateSuggestions($scoringResults, $userAnswer, $question);

        return $this->createResponse($totalScore, $feedback, $suggestions, $scoringResults);
    }

    /**
     * Score content relevance to the question/task
     */
    private function scoreContentRelevance(string $userAnswer, Question $question, string $exerciseType): array
    {
        $questionText = $question->prompt_text ?? '';
        $targetText = $question->target_text ?? '';
        
        // Use AI to analyze content relevance
        $relevanceScore = $this->analyzeWithAI('content_relevance', [
            'user_answer' => $userAnswer,
            'question' => $questionText,
            'target_answer' => $targetText,
            'exercise_type' => $exerciseType
        ]);

        $feedback = [];
        if ($relevanceScore >= 80) {
            $feedback[] = 'Excellent! Your answer directly addresses the question.';
        } elseif ($relevanceScore >= 60) {
            $feedback[] = 'Good relevance, but try to be more specific to the question.';
        } else {
            $feedback[] = 'Your answer seems off-topic. Please focus on the question asked.';
        }

        return [
            'score' => $relevanceScore,
            'feedback' => $feedback,
            'weight' => 0.20
        ];
    }

    /**
     * Advanced grammar accuracy scoring
     */
    private function scoreGrammarAccuracy(string $userAnswer): array
    {
        $grammarScore = $this->analyzeWithAI('grammar_accuracy', [
            'text' => $userAnswer
        ]);

        $feedback = [];
        if ($grammarScore >= 85) {
            $feedback[] = 'Excellent grammar! Your sentences are well-structured.';
        } elseif ($grammarScore >= 70) {
            $feedback[] = 'Good grammar overall, but check for minor errors.';
        } elseif ($grammarScore >= 50) {
            $feedback[] = 'Some grammar issues detected. Review subject-verb agreement and tenses.';
        } else {
            $feedback[] = 'Multiple grammar errors found. Consider reviewing basic grammar rules.';
        }

        return [
            'score' => $grammarScore,
            'feedback' => $feedback,
            'weight' => 0.25
        ];
    }

    /**
     * Vocabulary usage and sophistication
     */
    private function scoreVocabularyUsage(string $userAnswer, string $difficulty): array
    {
        $vocabScore = $this->analyzeWithAI('vocabulary_usage', [
            'text' => $userAnswer,
            'difficulty' => $difficulty
        ]);

        $feedback = [];
        if ($vocabScore >= 80) {
            $feedback[] = 'Great vocabulary! You used varied and appropriate words.';
        } elseif ($vocabScore >= 60) {
            $feedback[] = 'Good vocabulary, but try using more varied words.';
        } else {
            $feedback[] = 'Consider using more sophisticated vocabulary and avoiding repetition.';
        }

        return [
            'score' => $vocabScore,
            'feedback' => $feedback,
            'weight' => 0.15
        ];
    }

    /**
     * Coherence and logical structure
     */
    private function scoreCoherenceStructure(string $userAnswer): array
    {
        $coherenceScore = $this->analyzeWithAI('coherence_structure', [
            'text' => $userAnswer
        ]);

        $feedback = [];
        if ($coherenceScore >= 80) {
            $feedback[] = 'Excellent organization! Your ideas flow logically.';
        } elseif ($coherenceScore >= 60) {
            $feedback[] = 'Good structure, but some ideas could be better connected.';
        } else {
            $feedback[] = 'Try organizing your ideas more clearly with better transitions.';
        }

        return [
            'score' => $coherenceScore,
            'feedback' => $feedback,
            'weight' => 0.15
        ];
    }

    /**
     * Creativity and originality
     */
    private function scoreCreativityOriginality(string $userAnswer, string $targetAnswer): array
    {
        $creativityScore = $this->analyzeWithAI('creativity_originality', [
            'user_answer' => $userAnswer,
            'target_answer' => $targetAnswer
        ]);

        $feedback = [];
        if ($creativityScore >= 80) {
            $feedback[] = 'Very creative and original thinking!';
        } elseif ($creativityScore >= 60) {
            $feedback[] = 'Good creativity, but try to be more original.';
        } else {
            $feedback[] = 'Consider adding more personal insights and creative elements.';
        }

        return [
            'score' => $creativityScore,
            'feedback' => $feedback,
            'weight' => 0.10
        ];
    }

    /**
     * Length appropriateness
     */
    private function scoreLengthAppropriateness(string $userAnswer, string $targetAnswer, Question $question): array
    {
        $expectedLength = $question->expected_word_count ?? 0;
        $actualLength = str_word_count($userAnswer);
        
        $lengthScore = 0;
        $feedback = [];

        if ($expectedLength > 0) {
            $lengthRatio = $actualLength / $expectedLength;
            
            if ($lengthRatio >= 0.8 && $lengthRatio <= 1.2) {
                $lengthScore = 100;
                $feedback[] = 'Perfect length for this task!';
            } elseif ($lengthRatio >= 0.6 && $lengthRatio <= 1.4) {
                $lengthScore = 80;
                $feedback[] = 'Good length, but could be slightly adjusted.';
            } elseif ($lengthRatio < 0.6) {
                $lengthScore = 40;
                $feedback[] = 'Your answer is too short. Try to provide more details.';
            } else {
                $lengthScore = 60;
                $feedback[] = 'Your answer is too long. Try to be more concise.';
            }
        } else {
            // No expected length, use general guidelines
            if ($actualLength >= 10 && $actualLength <= 50) {
                $lengthScore = 100;
                $feedback[] = 'Good length for this type of answer.';
            } elseif ($actualLength < 10) {
                $lengthScore = 60;
                $feedback[] = 'Try to provide a more detailed answer.';
            } else {
                $lengthScore = 80;
                $feedback[] = 'Good length, but consider being more concise.';
            }
        }

        return [
            'score' => $lengthScore,
            'feedback' => $feedback,
            'weight' => 0.05
        ];
    }

    /**
     * Spelling accuracy
     */
    private function scoreSpellingAccuracy(string $userAnswer): array
    {
        $spellingScore = $this->analyzeWithAI('spelling_accuracy', [
            'text' => $userAnswer
        ]);

        $feedback = [];
        if ($spellingScore >= 90) {
            $feedback[] = 'Excellent spelling!';
        } elseif ($spellingScore >= 80) {
            $feedback[] = 'Good spelling with minor errors.';
        } else {
            $feedback[] = 'Check your spelling carefully.';
        }

        return [
            'score' => $spellingScore,
            'feedback' => $feedback,
            'weight' => 0.10
        ];
    }

    /**
     * Punctuation usage
     */
    private function scorePunctuationUsage(string $userAnswer): array
    {
        $punctuationScore = $this->analyzeWithAI('punctuation_usage', [
            'text' => $userAnswer
        ]);

        $feedback = [];
        if ($punctuationScore >= 90) {
            $feedback[] = 'Perfect punctuation usage!';
        } elseif ($punctuationScore >= 80) {
            $feedback[] = 'Good punctuation with minor issues.';
        } else {
            $feedback[] = 'Review punctuation rules and usage.';
        }

        return [
            'score' => $punctuationScore,
            'feedback' => $feedback,
            'weight' => 0.05
        ];
    }

    /**
     * Sentence variety and complexity
     */
    private function scoreSentenceVariety(string $userAnswer): array
    {
        $varietyScore = $this->analyzeWithAI('sentence_variety', [
            'text' => $userAnswer
        ]);

        $feedback = [];
        if ($varietyScore >= 80) {
            $feedback[] = 'Great sentence variety! You used different sentence structures.';
        } elseif ($varietyScore >= 60) {
            $feedback[] = 'Good variety, but try using more complex sentences.';
        } else {
            $feedback[] = 'Try varying your sentence structures and lengths.';
        }

        return [
            'score' => $varietyScore,
            'feedback' => $feedback,
            'weight' => 0.05
        ];
    }

    /**
     * Task completion based on exercise type
     */
    private function scoreTaskCompletion(string $userAnswer, string $exerciseType, string $targetAnswer, string $promptText, string $starterText): array
    {
        $completionScore = 0;
        $feedback = [];

        switch ($exerciseType) {
            case 'WAQ': // Answer the question
                $completionScore = $this->scoreAnswerQuestion($userAnswer, $targetAnswer, $promptText);
                $feedback[] = 'Answer completeness: ' . ($completionScore >= 70 ? 'Good' : 'Needs improvement');
                break;
                
            case 'WCS': // Complete the sentence
                $completionScore = $this->scoreCompleteSentence($userAnswer, $targetAnswer, $starterText);
                $feedback[] = 'Sentence completion: ' . ($completionScore >= 70 ? 'Good' : 'Needs improvement');
                break;
                
            case 'WSB': // Sentence Building
                $completionScore = $this->scoreSentenceBuilding($userAnswer, $targetAnswer, $promptText);
                $feedback[] = 'Sentence building: ' . ($completionScore >= 70 ? 'Good' : 'Needs improvement');
                break;
                
            case 'WSG': // Write sentence with given word
                $completionScore = $this->scoreSentenceWithWord($userAnswer, $targetAnswer, $promptText);
                $feedback[] = 'Word usage: ' . ($completionScore >= 70 ? 'Good' : 'Needs improvement');
                break;
                
            case 'WWO': // Word Order
                $completionScore = $this->scoreWordOrder($userAnswer, $targetAnswer, $promptText);
                $feedback[] = 'Word order: ' . ($completionScore >= 70 ? 'Good' : 'Needs improvement');
                break;
                
            default:
                $completionScore = 70; // Default score
                $feedback[] = 'Task completion: Good';
                break;
        }

        return [
            'score' => $completionScore,
            'feedback' => $feedback,
            'weight' => 0.20
        ];
    }

    /**
     * Calculate weighted total score
     */
    private function calculateWeightedScore(array $scoringResults, string $exerciseType): int
    {
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($scoringResults as $component => $result) {
            $totalScore += $result['score'] * $result['weight'];
            $totalWeight += $result['weight'];
        }

        return $totalWeight > 0 ? round($totalScore / $totalWeight) : 0;
    }

    /**
     * Generate AI-powered feedback
     */
    private function generateAIFeedback(string $userAnswer, array $scoringResults, Question $question, Exercise $exercise): array
    {
        $feedback = [];
        
        // Add component-specific feedback
        foreach ($scoringResults as $component => $result) {
            if ($result['score'] < 70) {
                $feedback = array_merge($feedback, $result['feedback']);
            }
        }

        // Add positive feedback for high scores
        foreach ($scoringResults as $component => $result) {
            if ($result['score'] >= 85) {
                $feedback = array_merge($feedback, $result['feedback']);
            }
        }

        // Generate overall AI feedback
        $overallFeedback = $this->analyzeWithAI('overall_feedback', [
            'user_answer' => $userAnswer,
            'question' => $question->prompt_text ?? '',
            'scoring_results' => $scoringResults
        ]);

        if ($overallFeedback) {
            $feedback[] = $overallFeedback;
        }

        return array_unique($feedback);
    }

    /**
     * Generate specific suggestions
     */
    private function generateSuggestions(array $scoringResults, string $userAnswer, Question $question): array
    {
        $suggestions = [];

        foreach ($scoringResults as $component => $result) {
            if ($result['score'] < 70) {
                switch ($component) {
                    case 'grammar_accuracy':
                        $suggestions[] = 'Review grammar rules, especially subject-verb agreement and tenses';
                        break;
                    case 'vocabulary_usage':
                        $suggestions[] = 'Try using more varied and sophisticated vocabulary';
                        break;
                    case 'coherence_structure':
                        $suggestions[] = 'Use transition words to connect your ideas better';
                        break;
                    case 'spelling_accuracy':
                        $suggestions[] = 'Double-check your spelling before submitting';
                        break;
                    case 'sentence_variety':
                        $suggestions[] = 'Mix simple, compound, and complex sentences';
                        break;
                }
            }
        }

        return array_unique($suggestions);
    }

    /**
     * Analyze text with AI
     */
    private function analyzeWithAI(string $analysisType, array $data): int
    {
        try {
            $prompt = $this->buildAIPrompt($analysisType, $data);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->openaiBaseUrl . '/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert English teacher and writing evaluator. Provide accurate scoring and feedback.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 150,
                'temperature' => 0.3
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '';
                
                // Extract score from AI response
                if (preg_match('/(\d+)/', $content, $matches)) {
                    return min(100, max(0, (int)$matches[1]));
                }
            }
        } catch (\Exception $e) {
            Log::error('AI Analysis Error: ' . $e->getMessage());
        }

        // Fallback to rule-based scoring
        return $this->fallbackScoring($analysisType, $data);
    }

    /**
     * Build AI prompt for specific analysis
     */
    private function buildAIPrompt(string $analysisType, array $data): string
    {
        $prompts = [
            'content_relevance' => "Analyze how well this answer addresses the question. Score 0-100: \"{$data['user_answer']}\" for question: \"{$data['question']}\"",
            'grammar_accuracy' => "Check grammar accuracy in this text. Score 0-100: \"{$data['text']}\"",
            'vocabulary_usage' => "Evaluate vocabulary usage and sophistication. Score 0-100: \"{$data['text']}\"",
            'coherence_structure' => "Analyze coherence and logical structure. Score 0-100: \"{$data['text']}\"",
            'creativity_originality' => "Evaluate creativity and originality. Score 0-100: \"{$data['user_answer']}\"",
            'spelling_accuracy' => "Check spelling accuracy. Score 0-100: \"{$data['text']}\"",
            'punctuation_usage' => "Check punctuation usage. Score 0-100: \"{$data['text']}\"",
            'sentence_variety' => "Evaluate sentence variety and complexity. Score 0-100: \"{$data['text']}\"",
            'overall_feedback' => "Provide overall feedback for this writing: \"{$data['user_answer']}\""
        ];

        return $prompts[$analysisType] ?? "Analyze this text. Score 0-100: \"{$data['text']}\"";
    }

    /**
     * Fallback scoring when AI is unavailable
     */
    private function fallbackScoring(string $analysisType, array $data): int
    {
        // Basic rule-based fallback scoring
        $text = $data['text'] ?? $data['user_answer'] ?? '';
        
        switch ($analysisType) {
            case 'grammar_accuracy':
                return $this->basicGrammarCheck($text);
            case 'spelling_accuracy':
                return $this->basicSpellingCheck($text);
            case 'vocabulary_usage':
                return $this->basicVocabularyCheck($text);
            default:
                return 70; // Default fallback score
        }
    }

    /**
     * Basic grammar check fallback
     */
    private function basicGrammarCheck(string $text): int
    {
        $score = 100;
        
        // Check for basic grammar issues
        if (!preg_match('/^[A-Z]/', $text)) $score -= 10;
        if (!preg_match('/[.!?]$/', $text)) $score -= 10;
        if (str_word_count($text) < 3) $score -= 20;
        
        return max(0, $score);
    }

    /**
     * Basic spelling check fallback
     */
    private function basicSpellingCheck(string $text): int
    {
        // Simple spelling check - in real implementation, use a dictionary
        $commonWords = ['the', 'and', 'is', 'are', 'was', 'were', 'have', 'has', 'had', 'will', 'would', 'could', 'should'];
        $words = explode(' ', strtolower($text));
        $correctWords = 0;
        
        foreach ($words as $word) {
            if (in_array($word, $commonWords) || strlen($word) <= 3) {
                $correctWords++;
            }
        }
        
        return count($words) > 0 ? round(($correctWords / count($words)) * 100) : 70;
    }

    /**
     * Basic vocabulary check fallback
     */
    private function basicVocabularyCheck(string $text): int
    {
        $words = explode(' ', strtolower($text));
        $uniqueWords = count(array_unique($words));
        $totalWords = count($words);
        
        if ($totalWords == 0) return 0;
        
        $variety = $uniqueWords / $totalWords;
        return round($variety * 100);
    }

    /**
     * Create standardized response
     */
    private function createResponse(int $score, array $feedback, array $suggestions, array $scoringResults = []): array
    {
        return [
            'score' => $score,
            'feedback' => $feedback,
            'suggestions' => $suggestions,
            'scoring_breakdown' => $scoringResults,
            'scoring_type' => 'advanced_ai',
            'timestamp' => now()->toISOString()
        ];
    }

    // Legacy methods for backward compatibility
    private function scoreAnswerQuestion($userAnswer, $targetAnswer, $promptText): int
    {
        if (empty($targetAnswer)) return 70;
        
        $similarity = $this->calculateSimilarity($userAnswer, $targetAnswer);
        if ($similarity >= 0.9) return 100;
        if ($similarity >= 0.7) return 80;
        if ($similarity >= 0.5) return 60;
        return 40;
    }

    private function scoreCompleteSentence($userAnswer, $targetAnswer, $starterText): int
    {
        if ($starterText && str_starts_with(strtolower($userAnswer), strtolower($starterText))) {
            return 90;
        }
        return 60;
    }

    private function scoreSentenceBuilding($userAnswer, $targetAnswer, $promptText): int
    {
        $promptWords = $this->extractWordsFromPrompt($promptText);
        $userWords = explode(' ', strtolower($userAnswer));
        
        $usedWords = 0;
        foreach ($promptWords as $word) {
            if (in_array($word, $userWords)) {
                $usedWords++;
            }
        }
        
        if ($usedWords === count($promptWords)) return 100;
        if ($usedWords >= count($promptWords) * 0.8) return 80;
        return 60;
    }

    private function scoreSentenceWithWord($userAnswer, $targetAnswer, $promptText): int
    {
        $targetWord = $this->extractTargetWord($promptText);
        if ($targetWord && str_contains(strtolower($userAnswer), $targetWord)) {
            return 90;
        }
        return 50;
    }

    private function scoreWordOrder($userAnswer, $targetAnswer, $promptText): int
    {
        return $this->scoreSentenceBuilding($userAnswer, $targetAnswer, $promptText);
    }

    private function extractWordsFromPrompt($promptText): array
    {
        if (preg_match('/Sắp xếp các từ:\s*(.+)/', $promptText, $matches)) {
            $wordsString = $matches[1];
            $words = array_map('trim', explode(',', $wordsString));
            return array_map('strtolower', $words);
        }
        return [];
    }

    private function extractTargetWord($promptText): string
    {
        $words = explode(' ', $promptText);
        return strtolower($words[count($words) - 1] ?? '');
    }

    private function calculateSimilarity($userAnswer, $targetAnswer): float
    {
        if (empty($targetAnswer)) return 0.0;
        
        $userWords = explode(' ', strtolower($userAnswer));
        $targetWords = explode(' ', strtolower($targetAnswer));
        
        $matches = 0;
        $totalWords = count($targetWords);
        
        foreach ($targetWords as $targetWord) {
            if (in_array($targetWord, $userWords)) {
                $matches++;
            }
        }
        
        return $totalWords > 0 ? $matches / $totalWords : 0.0;
    }
}
