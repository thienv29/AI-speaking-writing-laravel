<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Question;

/**
 * Service for evaluating writing attempts using Gemini API
 * Simplified - uses only Gemini API for scoring
 */
class AttemptService
{
    protected TemplateValidatorService $templateValidator;
    protected GeminiScoringService $geminiScoring;

    public function __construct(TemplateValidatorService $templateValidator, GeminiScoringService $geminiScoring)
    {
        $this->templateValidator = $templateValidator;
        $this->geminiScoring = $geminiScoring;
    }

    /**
     * Evaluate a writing attempt and create Attempt record
     */
    public function evaluateWritingAttempt(int $questionId, ?int $userId, string $userAnswer): Attempt
    {
        $question = Question::with(['exercise.type', 'exercise.lesson'])
            ->findOrFail($questionId);

        $cleanAnswer = trim($userAnswer);
        
        // Use Gemini API for all scoring
        $geminiResult = $this->geminiScoring->evaluate($question, $cleanAnswer);
        $result = $this->processGeminiResult($geminiResult);
        
        $attempt = Attempt::create([
            'user_id' => $userId,
            'question_id' => $question->id,
            'user_answer' => $cleanAnswer,
            'user_audio_url' => null,
            'is_correct' => $result['is_correct'],
            'feedback' => $result['feedback'],
            'created_at' => now(),
        ]);

        // Add metadata to response
        $metadata = [
            'score' => $result['score'] ?? null,
            'template_used' => $result['template_used'] ?? null,
            'extracted_value' => $result['extracted_value'] ?? null,
            'evaluation_meta' => $result['evaluation_meta'] ?? null,
        ];

        $this->loadAttemptRelations($attempt);
        
        // Add metadata back to attempt object after loading relations
        foreach ($metadata as $key => $value) {
            $attempt->{$key} = $value;
        }

        return $attempt;
    }

    public function evaluateSpeakingAttempt(int $questionId, int $userId, string $userAnswer, ?string $userAudioUrl=null): Attempt
    {
        $question = Question::findOrFail($questionId);

        $isCorrect = null;
        $feedback  = null;

        if (!empty($userAnswer)) {
            $normalizedAnswer = strtolower(trim(preg_replace('/[[:punct:]]+/u', '', $userAnswer)));
            $normalizedTarget = strtolower(trim(preg_replace('/[[:punct:]]+/u', '', $question->target_text ?? '')));

            $isCorrect = $this->matchesTarget($normalizedAnswer, $normalizedTarget);
            if (!$isCorrect && !empty($question->target_text_alt)) {
                $normalizedAlt = strtolower(trim(preg_replace('/[[:punct:]]+/u', '', $question->target_text_alt)));
                $isCorrect = $this->matchesTarget($normalizedAnswer, $normalizedAlt);
            }

            $feedback = $isCorrect
                ? 'Làm tốt lắm! Tiếp tục phát huy nhé.'
                : 'Hãy thử lại nào! Lần này đọc rõ ràng và chính xác hơn nhé.';
        }

        $attempt = Attempt::create([
            'user_id'        => $userId ?? 2,
            'question_id'    => $question->id,
            'user_answer'    => $userAnswer ?? null,
            'user_audio_url' => $userAudioUrl ?? null,
            'is_correct'     => $isCorrect,
            'feedback'       => $feedback,
            'created_at'     => now(),
        ]);

        // Load relations
        $attempt->load([
            'question:id,exercise_id,order_index',
            'question.exercise:id,lesson_id,type_id,title,instruction,difficulty,order_index',
            'user:id,name,email'
        ]);

        return $attempt;
    }

    /**
     * Get template hint for a question
     */
    public function getTemplateHint(int $questionId): ?string
    {
        $question = Question::findOrFail($questionId);
        return $this->templateValidator->getTemplateHint($question);
    }

    /**
     * Process Gemini scoring result
     */
    private function processGeminiResult(array $geminiResult): array
    {
        return [
            'is_correct' => $geminiResult['valid'] ?? false,
            'feedback' => $geminiResult['feedback'] ?? 'Good effort! Keep practicing.',
            'score' => $geminiResult['score'] ?? 70,
            'template_used' => $geminiResult['template_used'] ?? null,
            'extracted_value' => $geminiResult['extracted_value'] ?? null,
            'evaluation_meta' => $geminiResult['evaluation_meta'] ?? null,
        ];
    }

    private function matchesTarget(string $normalizedAnswer, string $normalizedTarget): bool
    {
        if ($normalizedTarget === '') {
            return false;
        }

        if ($normalizedAnswer === $normalizedTarget) {
            return true;
        }

        return str_starts_with($normalizedAnswer, $normalizedTarget . ' ');
    }

    /**
     * Load attempt relationships
     */
    private function loadAttemptRelations(Attempt $attempt): void
    {
        $attempt->load([
            'user:id,name,email',
            'question' => function ($query) {
                $query->select('id', 'exercise_id', 'order_index', 'prompt_text', 'target_text', 'starter_text')
                    ->with(['exercise' => function ($exerciseQuery) {
                        $exerciseQuery->select('id', 'title', 'type_id', 'lesson_id')
                            ->with(['type:id,code,name', 'lesson:id,title']);
                    }]);
            },
        ]);
    }
}
