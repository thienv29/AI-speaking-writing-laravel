<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Question;
use Illuminate\Support\Str;

class AttemptService
{
    protected $templateValidator;

    public function __construct(TemplateValidatorService $templateValidator)
    {
        $this->templateValidator = $templateValidator;
    }
    public function evaluateWritingAttempt(int $questionId, ?int $userId, string $userAnswer): Attempt
    {
        // Load Question với Exercise và ExerciseType để biết loại bài
        $question = Question::with(['exercise.type', 'exercise.lesson'])
            ->findOrFail($questionId);

        $cleanAnswer = trim($userAnswer);
        $exerciseTypeCode = $question->exercise->type->code;
        
        // Chấm điểm dựa trên loại bài tập
        $result = $this->evaluateByType($exerciseTypeCode, $question, $cleanAnswer);
        
        // Tạo Attempt
        $attempt = Attempt::create([
            'user_id'        => $userId,
            'question_id'    => $question->id,
            'user_answer'    => $cleanAnswer,
            'user_audio_url' => null,
            'is_correct'     => $result['is_correct'],
            'feedback'       => $result['feedback'],
        ]);

        // Add score to the response
        $attempt->score = $result['score'] ?? null;
        $attempt->template_used = $result['template_used'] ?? null;
        $attempt->extracted_value = $result['extracted_value'] ?? null;
        $attempt->evaluation_meta = $result['evaluation_meta'] ?? null;
        $attempt->effect = $result['effect'] ?? null;

        $attempt->load([
            'user:id,name,email',
            'question' => function ($query) {
                $query->select('id', 'exercise_id', 'order_index', 'prompt_text', 'target_text', 'starter_text')
                    ->with(['exercise' => function ($exerciseQuery) {
                        $exerciseQuery->select('id', 'title', 'type_id', 'lesson_id')
                            ->with([
                                'type:id,code,name',
                                'lesson:id,title'
                            ]);
                    }]);
            },
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

    private function evaluateByType(string $typeCode, Question $question, string $userAnswer): array
    {
        // Try template validation first
        if ($this->templateValidator->hasTemplate($question)) {
            $templateResult = $this->templateValidator->evaluate($question, $userAnswer);
            
            // If template validation succeeded, return it
            if ($templateResult['valid']) {
                return [
                    'is_correct' => true,
                    'feedback' => $templateResult['feedback'],
                    // pass through score; do not coerce to 100 for binary templates
                    'score' => $templateResult['score'] ?? null,
                    'template_used' => $templateResult['template_used'] ?? null,
                    'extracted_value' => $templateResult['extracted_value'] ?? null,
                    'evaluation_meta' => $templateResult['evaluation_meta'] ?? null,
                    'effect' => $templateResult['effect'] ?? null
                ];
            }
            
            // If template validation failed, return template feedback
            return [
                'is_correct' => false,
                'feedback' => $templateResult['feedback'],
                // for binary templates, keep score null; graded templates may return 0
                'score' => $templateResult['score'] ?? null,
                'template_used' => $templateResult['template_used'] ?? null,
                'extracted_value' => $templateResult['extracted_value'] ?? null,
                'evaluation_meta' => $templateResult['evaluation_meta'] ?? null,
                'effect' => $templateResult['effect'] ?? null
            ];
        }
        
        // Fallback to original evaluation methods
        switch ($typeCode) {
            case 'WAQ': // Answer the question
                return $this->evaluateAnswerQuestion($question, $userAnswer);
            case 'WCS': // Complete the sentence
                return $this->evaluateCompleteSentence($question, $userAnswer);
            case 'WSG': // Write sentence using given word
                return $this->evaluateWriteSentence($question, $userAnswer);
            default:
                return $this->evaluateGeneric($question, $userAnswer);
        }
    }

    private function evaluateAnswerQuestion(Question $question, string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        // Phân tích câu hỏi để xác định yêu cầu độ dài
        $questionText = strtolower($question->prompt_text ?? '');
        $expectedLength = $this->getExpectedAnswerLength($questionText);
        
        // Kiểm tra độ dài phù hợp với loại câu hỏi
        $answerLength = mb_strlen($userAnswer);
        if ($answerLength >= $expectedLength['min']) {
            $score += 30;
            
            // Bonus nếu đủ dài cho câu hỏi mở
            if ($expectedLength['min'] > 10 && $answerLength >= $expectedLength['good']) {
                $score += 10;
            }
        } else {
            $reasons[] = "Câu trả lời quá ngắn! (Cần ít nhất {$expectedLength['min']} ký tự)";
        }
        
        // So sánh với target_text nếu có
        if ($question->target_text) {
            $targetWords = collect(preg_split('/\s+/', Str::lower($question->target_text)))->filter();
            $answerWords = collect(preg_split('/\s+/', Str::lower($userAnswer)))->filter();
            $overlap = $answerWords->intersect($targetWords)->count();
            
            if ($targetWords->count() > 0) {
                $coverage = $overlap / $targetWords->count();
                
                // Nếu có ít nhất 1 từ trùng khớp thì cho điểm
                if ($coverage > 0) {
                    $score += 30; // Điểm cố định nếu có từ trùng
                } else {
                    $reasons[] = "Chưa trả lời đúng chủ đề!";
                }
                
                // Bonus điểm nếu trùng nhiều từ
                if ($coverage >= 0.5) {
                    $score += 15; // Bonus 15 điểm
                }
            }
        } else {
            // Nếu không có target_text, cho điểm dựa trên độ dài phù hợp
            if ($answerLength >= $expectedLength['good']) {
                $score += 30;
            } else {
                $score += 20;
            }
        }
        
        // Kiểm tra ngữ pháp cơ bản
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 15;
        } else {
            $reasons[] = "Thiếu dấu câu cuối câu!";
        }
        
        // Kiểm tra viết hoa đầu câu
        if (preg_match('/^[A-Z]/', $userAnswer)) {
            $score += 15;
        } else {
            $reasons[] = "Cần viết hoa đầu câu!";
        }
        
        // Giới hạn tối đa 100 điểm
        $score = min($score, 100);
        
        $isCorrect = $score >= 60;
        $feedback = $this->generateFeedback($score, $reasons, "Trả lời câu hỏi");
        
        return ['is_correct' => $isCorrect, 'feedback' => $feedback];
    }
    
    private function getExpectedAnswerLength(string $questionText): array
    {
        // Câu hỏi đơn giản - chỉ cần trả lời ngắn
        $simpleQuestions = ['what is your name', 'how old are you', 'where are you from', 'what time is it'];
        foreach ($simpleQuestions as $pattern) {
            if (strpos($questionText, $pattern) !== false) {
                return ['min' => 5, 'good' => 15];
            }
        }
        
        // Câu hỏi mở - cần trả lời dài
        $openQuestions = ['describe', 'tell me about', 'explain', 'why', 'how do you', 'what do you think'];
        foreach ($openQuestions as $pattern) {
            if (strpos($questionText, $pattern) !== false) {
                return ['min' => 20, 'good' => 50];
            }
        }
        
        // Câu hỏi trung bình
        return ['min' => 10, 'good' => 25];
    }

    private function evaluateCompleteSentence(Question $question, string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        // Kiểm tra có hoàn thành câu không
        if ($question->starter_text && $userAnswer) {
            $fullSentence = $question->starter_text . ' ' . $userAnswer;
            if (mb_strlen($fullSentence) >= 10) {
                $score += 50;
            } else {
                $reasons[] = "Câu chưa hoàn chỉnh!";
            }
        }
        
        // So sánh với target_text
        if ($question->target_text) {
            $targetWords = collect(preg_split('/\s+/', Str::lower($question->target_text)))->filter();
            $answerWords = collect(preg_split('/\s+/', Str::lower($userAnswer)))->filter();
            $overlap = $answerWords->intersect($targetWords)->count();
            
            if ($targetWords->count() > 0) {
                $coverage = $overlap / $targetWords->count();
                $score += $coverage * 30;
                
                if ($coverage < 0.4) {
                    $reasons[] = "Chưa điền đúng từ!";
                }
            }
        }
        
        // Kiểm tra dấu câu
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 20;
        } else {
            $reasons[] = "Thiếu dấu câu!";
        }
        
        $isCorrect = $score >= 60;
        $feedback = $this->generateFeedback($score, $reasons, "Hoàn thành câu");
        
        return ['is_correct' => $isCorrect, 'feedback' => $feedback];
    }

    private function evaluateWriteSentence(Question $question, string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        // Kiểm tra có sử dụng từ cho sẵn không
        if ($question->prompt_text) {
            $givenWord = Str::lower(trim($question->prompt_text));
            $answerLower = Str::lower($userAnswer);
            
            if (str_contains($answerLower, $givenWord)) {
                $score += 50;
            } else {
                $reasons[] = "Chưa sử dụng từ '" . $question->prompt_text . "'!";
            }
        }
        
        // Kiểm tra độ dài câu
        if (mb_strlen($userAnswer) >= 8) {
            $score += 30;
        } else {
            $reasons[] = "Câu quá ngắn!";
        }
        
        // Kiểm tra ngữ pháp cơ bản
        if (preg_match('/^[A-Z]/', $userAnswer) && preg_match('/[.!?]$/', $userAnswer)) {
            $score += 20;
        } else {
            $reasons[] = "Cần viết hoa đầu câu và có dấu câu!";
        }
        
        $isCorrect = $score >= 60;
        $feedback = $this->generateFeedback($score, $reasons, "Đặt câu");
        
        return ['is_correct' => $isCorrect, 'feedback' => $feedback];
    }

    private function evaluateGeneric(Question $question, string $userAnswer): array
    {
        $score = mb_strlen($userAnswer) >= 10 ? 70 : 30;
        $isCorrect = $score >= 60;
        $feedback = $isCorrect ? "Tốt lắm! 🌟" : "Cần cố gắng thêm! 💪";
        
        return ['is_correct' => $isCorrect, 'feedback' => $feedback];
    }

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
}