<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Question;
use Illuminate\Support\Str;

/**
 * Service for evaluating writing attempts
 */
class AttemptService
{
    protected TemplateValidatorService $templateValidator;

    public function __construct(TemplateValidatorService $templateValidator)
    {
        $this->templateValidator = $templateValidator;
    }

    /**
     * Evaluate a writing attempt and create Attempt record
     */
    public function evaluateWritingAttempt(int $questionId, ?int $userId, string $userAnswer): Attempt
    {
        $question = Question::with(['exercise.type', 'exercise.lesson'])
            ->findOrFail($questionId);

        $cleanAnswer = trim($userAnswer);
        $exerciseTypeCode = $question->exercise->type->code;
        $result = $this->evaluateByType($exerciseTypeCode, $question, $cleanAnswer);
        
        $attempt = Attempt::create([
            'user_id' => $userId,
            'question_id' => $question->id,
            'user_answer' => $cleanAnswer,
            'user_audio_url' => null,
            'is_correct' => $result['is_correct'],
            'feedback' => $result['feedback'],
        ]);

        // Add metadata to response (these will be lost after loadAttemptRelations)
        // So we need to store them separately and add back after loading
        $metadata = [
            'score' => $result['score'] ?? null,
            'template_used' => $result['template_used'] ?? null,
            'extracted_value' => $result['extracted_value'] ?? null,
            'evaluation_meta' => $result['evaluation_meta'] ?? null,
            'effect' => $result['effect'] ?? null,
        ];

        $this->loadAttemptRelations($attempt);
        
        // Add metadata back to attempt object after loading relations
        foreach ($metadata as $key => $value) {
            $attempt->{$key} = $value;
        }

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
     * Evaluate answer by exercise type
     */
    private function evaluateByType(string $typeCode, Question $question, string $userAnswer): array
    {
        // Try template validation first
        if ($this->templateValidator->hasTemplate($question)) {
            return $this->processTemplateResult($this->templateValidator->evaluate($question, $userAnswer));
        }
        
        // Fallback to type-specific evaluation
        switch ($typeCode) {
            case 'WAQ':
                return $this->evaluateAnswerQuestion($question, $userAnswer);
            case 'WCS':
                return $this->evaluateCompleteSentence($question, $userAnswer);
            case 'WSG':
                return $this->evaluateWriteSentence($question, $userAnswer);
            default:
                return $this->evaluateGeneric($question, $userAnswer);
        }
    }

    /**
     * Process template validation result
     */
    private function processTemplateResult(array $templateResult): array
    {
        return [
            'is_correct' => $templateResult['valid'],
            'feedback' => $templateResult['feedback'],
            'score' => $templateResult['score'] ?? null,
            'template_used' => $templateResult['template_used'] ?? null,
            'extracted_value' => $templateResult['extracted_value'] ?? null,
            'evaluation_meta' => $templateResult['evaluation_meta'] ?? null,
            'effect' => $templateResult['effect'] ?? null,
        ];
    }

    /**
     * Evaluate WAQ (Answer the question) type
     */
    private function evaluateAnswerQuestion(Question $question, string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        $answerLength = mb_strlen($userAnswer);
        
        $questionText = strtolower($question->prompt_text ?? '');
        $expectedLength = $this->getExpectedAnswerLength($questionText);
        
        // Length check
        if ($answerLength >= $expectedLength['min']) {
            $score += 30;
            if ($expectedLength['min'] > 10 && $answerLength >= $expectedLength['good']) {
                $score += 10;
            }
        } else {
            $reasons[] = "Câu trả lời quá ngắn! (Cần ít nhất {$expectedLength['min']} ký tự)";
        }
        
        // Target text comparison
        if ($question->target_text) {
            $coverage = $this->calculateWordCoverage($question->target_text, $userAnswer);
            if ($coverage > 0) {
                $score += 30;
                if ($coverage >= 0.5) {
                    $score += 15;
                }
            } else {
                $reasons[] = "Chưa trả lời đúng chủ đề!";
            }
        } else {
            $score += $answerLength >= $expectedLength['good'] ? 30 : 20;
        }
        
        // Grammar checks
        $score += $this->checkGrammar($userAnswer, $reasons);
        
        return $this->buildResult(min($score, 100), $reasons, "Trả lời câu hỏi");
    }

    /**
     * Evaluate WCS (Complete the sentence) type
     */
    private function evaluateCompleteSentence(Question $question, string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        // Check sentence completion
        if ($question->starter_text && $userAnswer) {
            $fullSentence = $question->starter_text . ' ' . $userAnswer;
            if (mb_strlen($fullSentence) >= 10) {
                $score += 50;
            } else {
                $reasons[] = "Câu chưa hoàn chỉnh!";
            }
        }
        
        // Target text comparison
        if ($question->target_text) {
            $coverage = $this->calculateWordCoverage($question->target_text, $userAnswer);
            $score += $coverage * 30;
            if ($coverage < 0.4) {
                $reasons[] = "Chưa điền đúng từ!";
            }
        }
        
        // Punctuation check
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 20;
        } else {
            $reasons[] = "Thiếu dấu câu!";
        }
        
        return $this->buildResult($score, $reasons, "Hoàn thành câu");
    }

    /**
     * Evaluate WSG (Write sentence using given word) type
     */
    private function evaluateWriteSentence(Question $question, string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        // Check word usage
        if ($question->prompt_text) {
            $givenWord = Str::lower(trim($question->prompt_text));
            if (str_contains(Str::lower($userAnswer), $givenWord)) {
                $score += 50;
            } else {
                $reasons[] = "Chưa sử dụng từ '{$question->prompt_text}'!";
            }
        }
        
        // Length check
        if (mb_strlen($userAnswer) >= 8) {
            $score += 30;
        } else {
            $reasons[] = "Câu quá ngắn!";
        }
        
        // Grammar check (capitalization and punctuation)
        $grammarScore = 0;
        if (preg_match('/^[A-Z]/', $userAnswer) && preg_match('/[.!?]$/', $userAnswer)) {
            $grammarScore = 20;
        } else {
            $reasons[] = "Cần viết hoa đầu câu và có dấu câu!";
        }
        
        // NEW: Check spelling and grammar errors
        $spellingErrors = $this->checkSpellingErrors($userAnswer);
        $grammarErrors = $this->checkGrammarErrors($userAnswer);
        
        // Apply penalties for errors
        $penalty = 0;
        if (!empty($spellingErrors)) {
            $penalty += count($spellingErrors) * 15; // -15 points per spelling error
            $reasons[] = "Có lỗi chính tả: " . implode(', ', array_slice($spellingErrors, 0, 3));
        }
        
        if (!empty($grammarErrors)) {
            $penalty += count($grammarErrors) * 10; // -10 points per grammar error
            $reasons[] = "Có lỗi ngữ pháp: " . implode(', ', array_slice($grammarErrors, 0, 3));
        }
        
        // Apply penalty
        $score += $grammarScore - $penalty;
        
        // Critical: If there are serious errors, cap the score at 70
        if (!empty($spellingErrors) || !empty($grammarErrors)) {
            $score = min($score, 70);
            if ($score >= 60) {
                $reasons[] = "Cần sửa lỗi chính tả và ngữ pháp để đạt điểm cao hơn!";
            }
        }
        
        return $this->buildResult(max(0, $score), $reasons, "Đặt câu");
    }
    
    /**
     * Check spelling errors in a sentence
     * 
     * @param string $sentence
     * @return array Array of misspelled words
     */
    private function checkSpellingErrors(string $sentence): array
    {
        $errors = [];
        
        // Split sentence into words
        $words = preg_split('/\s+/', strtolower($sentence));
        $cleanWords = array_map(function($word) {
            // Remove punctuation but keep letters
            return preg_replace('/[^a-z]/', '', strtolower($word));
        }, $words);
        
        // Common English words dictionary (basic check)
        $commonWords = [
            'i', 'you', 'he', 'she', 'it', 'we', 'they',
            'am', 'is', 'are', 'was', 'were', 'be', 'been',
            'have', 'has', 'had', 'do', 'does', 'did', 'done',
            'will', 'would', 'can', 'could', 'should', 'may', 'might',
            'a', 'an', 'the', 'this', 'that', 'these', 'those',
            'my', 'your', 'his', 'her', 'its', 'our', 'their',
            'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from',
            'and', 'or', 'but', 'so', 'because', 'if', 'when', 'where',
            'love', 'like', 'enjoy', 'hate', 'want', 'need', 'like',
            'sunny', 'rainy', 'cloudy', 'windy', 'snowy', 'weather',
            'really', 'very', 'quite', 'too', 'so', 'much', 'many',
            'today', 'tomorrow', 'yesterday', 'now', 'then',
            'morning', 'afternoon', 'evening', 'night',
            'good', 'bad', 'nice', 'great', 'wonderful', 'beautiful',
            'big', 'small', 'new', 'old', 'young', 'happy', 'sad'
        ];
        
        foreach ($cleanWords as $word) {
            if (strlen($word) < 2) continue; // Skip very short words
            
            // Check against common words
            if (in_array($word, $commonWords)) {
                continue;
            }
            
            // Use SpellChecker to check if word is valid
            // Try Translation API as fallback
            $isValid = $this->checkWordValidity($word);
            
            if (!$isValid) {
                $errors[] = $word;
            }
        }
        
        return $errors;
    }
    
    /**
     * Check if a word is valid using Translation API
     * 
     * @param string $word
     * @return bool
     */
    private function checkWordValidity(string $word): bool
    {
        // Skip if word is too short
        if (strlen($word) < 2 || strlen($word) > 50) {
            return false;
        }
        
        try {
            $translation = \App\Services\TranslationService::translate($word);
            
            // If translation succeeds and is meaningful, word is likely valid
            if ($translation['source'] === 'api' || 
                $translation['source'] === 'dictionary' || 
                $translation['source'] === 'cache') {
                $translatedText = $translation['translation'];
                
                if (!empty($translatedText) && 
                    $translatedText !== 'Không tìm thấy bản dịch' &&
                    strlen($translatedText) > 0) {
                    // If translation contains Vietnamese characters or is different from original
                    if ($translatedText !== $word || preg_match('/[àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđ]/u', $translatedText)) {
                        return true; // Word is valid
                    }
                }
            }
        } catch (\Throwable $e) {
            // If translation fails, assume word might be invalid
            return false;
        }
        
        return false;
    }
    
    /**
     * Check grammar errors in a sentence
     * 
     * @param string $sentence
     * @return array Array of grammar error descriptions
     */
    private function checkGrammarErrors(string $sentence): array
    {
        $errors = [];
        $lowerSentence = strtolower($sentence);
        
        // Load grammar patterns from config file
        $patterns = config('spellchecker.grammar_patterns', []);
        
        foreach ($patterns as $pattern => $message) {
            if (preg_match($pattern, $lowerSentence)) {
                $errors[] = $message;
            }
        }
        
        return $errors;
    }

    /**
     * Generic evaluation fallback
     */
    private function evaluateGeneric(Question $question, string $userAnswer): array
    {
        $score = mb_strlen($userAnswer) >= 10 ? 70 : 30;
        $isCorrect = $score >= 60;
        $feedback = $isCorrect ? "Tốt lắm! 🌟" : "Cần cố gắng thêm! 💪";
        
        return ['is_correct' => $isCorrect, 'feedback' => $feedback, 'score' => $score];
    }

    /**
     * Get expected answer length based on question type
     */
    private function getExpectedAnswerLength(string $questionText): array
    {
        $simpleQuestions = ['what is your name', 'how old are you', 'where are you from', 'what time is it'];
        foreach ($simpleQuestions as $pattern) {
            if (strpos($questionText, $pattern) !== false) {
                return ['min' => 5, 'good' => 15];
            }
        }
        
        $openQuestions = ['describe', 'tell me about', 'explain', 'why', 'how do you', 'what do you think'];
        foreach ($openQuestions as $pattern) {
            if (strpos($questionText, $pattern) !== false) {
                return ['min' => 20, 'good' => 50];
            }
        }
        
        return ['min' => 10, 'good' => 25];
    }

    /**
     * Calculate word coverage between target and answer
     */
    private function calculateWordCoverage(string $targetText, string $userAnswer): float
    {
        $targetWords = collect(preg_split('/\s+/', Str::lower($targetText)))->filter();
        $answerWords = collect(preg_split('/\s+/', Str::lower($userAnswer)))->filter();
        
        if ($targetWords->isEmpty()) {
            return 0;
        }
        
        $overlap = $answerWords->intersect($targetWords)->count();
        return $overlap / $targetWords->count();
    }

    /**
     * Check grammar (punctuation and capitalization)
     */
    private function checkGrammar(string $userAnswer, array &$reasons): int
    {
        $score = 0;
        
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 15;
        } else {
            $reasons[] = "Thiếu dấu câu cuối câu!";
        }
        
        if (preg_match('/^[A-Z]/', $userAnswer)) {
            $score += 15;
        } else {
            $reasons[] = "Cần viết hoa đầu câu!";
        }
        
        return $score;
    }

    /**
     * Build result array from score and reasons
     */
    private function buildResult(int $score, array $reasons, string $type): array
    {
        $isCorrect = $score >= 60;
        $feedback = $this->generateFeedback($score, $reasons, $type);
        
        return [
            'is_correct' => $isCorrect,
            'feedback' => $feedback,
            'score' => $score,
        ];
    }

    /**
     * Generate feedback message
     */
    private function generateFeedback(int $score, array $reasons, string $type): string
    {
        $feedback = "Điểm: {$score}/100 - ";
        
        if ($score >= 80) {
            $feedback .= "Xuất sắc! 🌟";
        } elseif ($score >= 60) {
            $feedback .= "Tốt lắm! 👍";
        } else {
            $feedback .= "Cần cố gắng thêm! 💪";
        }
        
        if (!empty($reasons)) {
            $feedback .= " | " . implode(" ", $reasons);
        }
        
        return $feedback;
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
