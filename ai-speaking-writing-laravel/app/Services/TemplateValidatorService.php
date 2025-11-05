<?php

namespace App\Services;

use App\Models\Question;
use App\Services\GeminiScoringService;

/**
 * Service for validating user answers using Gemini API
 * Simplified - delegates all scoring to Gemini API
 */
class TemplateValidatorService
{
    protected GeminiScoringService $geminiScoring;
    
    public function __construct(GeminiScoringService $geminiScoring)
    {
        $this->geminiScoring = $geminiScoring;
    }

    /**
     * Template hints for different question types (for UI hints only)
     */
    private array $templateHints = [
        'name' => 'Format: My name is [your name]',
        'age' => 'Format: I am [number] years old',
        'hobby' => 'Format: My favorite hobby is [hobby]',
        'location' => 'Format: I live in [place]',
        'greeting' => 'Format: Hello, [name]',
        'time' => 'Format: It is [hour] o\'clock',
        'weather' => 'Format: Today is [sunny/rainy/cloudy/windy/snowy]',
        'word_usage' => 'Write a complete sentence using the word',
    ];

    /**
     * Evaluate answer using Gemini API
     */
    public function evaluate(Question $question, string $userAnswer): array
    {
        return $this->geminiScoring->evaluate($question, $userAnswer);
    }

    /**
     * Detect question type based on prompt_text (for hints only)
     */
    private function detectQuestionType(Question $question): ?string
    {
        $prompt = strtolower($question->prompt_text ?? '');
        $detectionMap = [
            'name' => ['what is your name', 'your name'],
            'age' => ['how old', 'your age', 'years old'],
            'hobby' => ['hobby', 'favorite'],
            'location' => ['where', 'live'],
            'greeting' => ['hello', 'greet'],
            'time' => ['time', 'o\'clock'],
            'weather' => ['weather', 'today is'],
        ];

        foreach ($detectionMap as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($prompt, $keyword) !== false) {
                    return $type;
                }
            }
        }

        // Word usage questions (WSG) - short prompt
        if ($question->prompt_text && strlen($question->prompt_text) < 50) {
            return 'word_usage';
        }

        return null;
    }

    /**
     * Get template hint for a question
     */
    public function getTemplateHint(Question $question): ?string
    {
        $questionType = $this->detectQuestionType($question);
        
        if ($questionType && isset($this->templateHints[$questionType])) {
            return $this->templateHints[$questionType];
        }
        
        return null;
    }

    /**
     * Check if question has a template hint
     */
    public function hasTemplate(Question $question): bool
    {
        return $this->detectQuestionType($question) !== null;
    }
}
