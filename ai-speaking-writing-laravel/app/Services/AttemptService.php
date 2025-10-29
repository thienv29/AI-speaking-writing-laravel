<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Question;
use Illuminate\Support\Str;

class AttemptService
{
    public function evaluateWritingAttempt(int $questionId, ?int $userId, string $userAnswer): Attempt
    {
        // Load Question với Exercise và ExerciseType để biết loại bài
        $question = Question::with(['exercise.type'])
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

        return $attempt->load(['question:id,exercise_id,order_index', 'question.exercise:id,title', 'user:id,name,email']);
    }

    private function evaluateByType(string $typeCode, Question $question, string $userAnswer): array
    {
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
        
        // Kiểm tra có trả lời không
        if (mb_strlen($userAnswer) >= 5) {
            $score += 40;
        } else {
            $reasons[] = "Câu trả lời quá ngắn!";
        }
        
        // So sánh với target_text nếu có
        if ($question->target_text) {
            $targetWords = collect(preg_split('/\s+/', Str::lower($question->target_text)))->filter();
            $answerWords = collect(preg_split('/\s+/', Str::lower($userAnswer)))->filter();
            $overlap = $answerWords->intersect($targetWords)->count();
            
            if ($targetWords->count() > 0) {
                $coverage = $overlap / $targetWords->count();
                $score += $coverage * 40;
                
                if ($coverage < 0.3) {
                    $reasons[] = "Chưa trả lời đúng chủ đề!";
                }
            }
        }
        
        // Kiểm tra ngữ pháp cơ bản
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 20;
        } else {
            $reasons[] = "Thiếu dấu câu cuối câu!";
        }
        
        $isCorrect = $score >= 60;
        $feedback = $this->generateFeedback($score, $reasons, "Trả lời câu hỏi");
        
        return ['is_correct' => $isCorrect, 'feedback' => $feedback];
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