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
     * Evaluate answer using Gemini API
     */
    public function evaluate(Question $question, string $userAnswer): array
    {
        return $this->geminiScoring->evaluate($question, $userAnswer);
    }

    /**
     * Get template hint for a question
     */
    public function getTemplateHint(Question $question): ?string
    {
        if (!$question->relationLoaded('exercises')) {
            $question->load('exercises.type');
        }
        $exercise = $question->exercises->first();
        $exerciseType = $exercise && $exercise->relationLoaded('type') ? $exercise->type : ($exercise ? $exercise->type : null);
        $code = strtoupper($exerciseType->code ?? '');

        if ($question->starter_text) {
            return sprintf('Hoàn thành câu: "%s ..."', trim($question->starter_text));
        }

        $hint = null;

        switch ($code) {
            case 'WCS':
                $hint = 'Hoàn thành câu theo gợi ý đã cho, viết thành câu đầy đủ nhé con!';
                break;
            case 'WAQ':
                $hint = 'Con trả lời theo ý của mình, miễn là đúng chủ đề và viết thành câu hoàn chỉnh.';
                break;
            case 'WSG':
                $hint = 'Dùng từ được cho để đặt một câu hoàn chỉnh.';
                break;
        }
        
        return $hint;
    }

    /**
     * Check if question has a template hint
     */
    public function hasTemplate(Question $question): bool
    {
        return $this->getTemplateHint($question) !== null;
    }
}
