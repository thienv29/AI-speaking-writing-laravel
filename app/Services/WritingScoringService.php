<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Question;

class WritingScoringService
{
    /**
     * Score writing answer based on exercise type
     */
    public function scoreAnswer(Exercise $exercise, Question $question, string $userAnswer): array
    {
        $originalAnswer = trim($userAnswer);
        $userAnswer = trim(strtolower($userAnswer));
        $score = 0;
        $feedback = [];
        $suggestions = [];

        // Basic validation
        if (empty($userAnswer)) {
            return [
                'score' => 0,
                'feedback' => ['Please provide an answer'],
                'suggestions' => ['Try to write something related to the question'],
                'scoring_type' => 'demo'
            ];
        }

        // Get exercise type and scoring based on exercise purpose
        $exerciseType = $exercise->type->code;
        $targetAnswer = trim(strtolower($question->target_text ?? ''));
        $promptText = $question->prompt_text ?? '';
        $starterText = $question->starter_text ?? '';
        
        // Check for exact match first - if perfect, give 100 points
        if ($targetAnswer && $userAnswer === $targetAnswer) {
            $score = 100;
            $feedback[] = 'Perfect! Exact match with correct answer! 🎯';
            
            // Check grammar bonus
            if (preg_match('/^[A-Z].*[.!?]$/', $originalAnswer)) {
                $feedback[] = 'Perfect sentence structure!';
            }
            
            return [
                'score' => 100,
                'feedback' => $feedback,
                'suggestions' => $suggestions,
                'scoring_type' => 'demo'
            ];
        }
        
        // Scoring based on exercise type for non-perfect answers
        switch ($exerciseType) {
            case 'WAQ': // Writing - Answer the question
                $score += $this->scoreAnswerQuestion($userAnswer, $targetAnswer, $promptText);
                break;
                
            case 'WCS': // Writing - Complete the sentence
                $score += $this->scoreCompleteSentence($userAnswer, $targetAnswer, $starterText);
                break;
                
            case 'WSB': // Writing - Sentence Building
                $score += $this->scoreSentenceBuilding($userAnswer, $targetAnswer, $promptText);
                break;
                
            case 'WSG': // Writing - Write sentence using given word
                $score += $this->scoreSentenceWithWord($userAnswer, $targetAnswer, $promptText);
                break;
                
            case 'WWO': // Writing - Word Order
                $score += $this->scoreWordOrder($userAnswer, $targetAnswer, $promptText);
                break;
                
            default:
                $score += $this->scoreGeneric($userAnswer, $targetAnswer);
                break;
        }

        // Grammar and sentence structure (25 points)
        if (preg_match('/^[A-Z].*[.!?]$/', $originalAnswer)) {
            $score += 25;
            $feedback[] = 'Perfect sentence structure!';
        } else if (preg_match('/^[A-Z]/', $originalAnswer)) {
            $score += 15;
            $feedback[] = 'Good start with capital letter';
            $suggestions[] = 'Remember to end with punctuation (. ! ?)';
        } else {
            $score += 5;
            $feedback[] = 'Try to write a complete sentence';
            $suggestions[] = 'Start with a capital letter and end with punctuation';
        }

        // Length appropriateness (15 points)
        $wordCount = str_word_count($userAnswer);
        if ($targetAnswer) {
            $targetWordCount = str_word_count($targetAnswer);
            if (abs($wordCount - $targetWordCount) <= 1) {
                $score += 15;
                $feedback[] = 'Good answer length!';
            } else if (abs($wordCount - $targetWordCount) <= 2) {
                $score += 10;
                $feedback[] = 'Answer length is close to expected';
            } else {
                $score += 5;
                $feedback[] = 'Check the expected answer length';
            }
        } else {
            if ($wordCount >= 3) {
                $score += 15;
                $feedback[] = 'Good answer length';
            } else {
                $score += 5;
                $feedback[] = 'Answer is too short';
            }
        }

        // Spelling and accuracy (10 points)
        $spellingScore = $this->calculateSpellingScore($userAnswer, $targetAnswer);
        $score += $spellingScore;
        
        if ($spellingScore >= 8) {
            $feedback[] = 'Good spelling!';
        } else if ($spellingScore >= 5) {
            $feedback[] = 'Check your spelling';
        }

        // Bonus for very good answers
        if ($score >= 80) {
            $score = min(100, $score + 5);
            $feedback[] = 'Great work! 🌟';
        }

        return [
            'score' => min(100, $score),
            'feedback' => $feedback,
            'suggestions' => $suggestions,
            'scoring_type' => 'demo'
        ];
    }

    /**
     * Score Answer Question exercises (WAQ)
     */
    private function scoreAnswerQuestion($userAnswer, $targetAnswer, $promptText): int
    {
        // For answer questions, target_text is the expected answer
        if ($targetAnswer && $userAnswer === $targetAnswer) {
            return 50;
        } else if ($targetAnswer && $this->calculateSimilarity($userAnswer, $targetAnswer) >= 0.8) {
            return 40;
        } else if ($targetAnswer && $this->calculateSimilarity($userAnswer, $targetAnswer) >= 0.6) {
            return 30;
        } else {
            return 10;
        }
    }

    /**
     * Score Complete Sentence exercises (WCS)
     */
    private function scoreCompleteSentence($userAnswer, $targetAnswer, $starterText): int
    {
        // Check if answer starts with the starter text
        if ($starterText && str_starts_with(strtolower($userAnswer), strtolower($starterText))) {
            return 30;
        } else {
            return 10;
        }
    }

    /**
     * Score Sentence Building exercises (WSB)
     */
    private function scoreSentenceBuilding($userAnswer, $targetAnswer, $promptText): int
    {
        // Extract words from prompt_text (the words to arrange)
        $promptWords = $this->extractWordsFromPrompt($promptText);
        $userWords = explode(' ', $userAnswer);
        
        // Check if all required words are used
        $usedWords = 0;
        foreach ($promptWords as $word) {
            if (in_array($word, $userWords)) {
                $usedWords++;
            }
        }
        
        if ($usedWords === count($promptWords)) {
            return 30;
        } else {
            return 15;
        }
    }

    /**
     * Score Sentence with Given Word exercises (WSG)
     */
    private function scoreSentenceWithWord($userAnswer, $targetAnswer, $promptText): int
    {
        // Extract the target word from prompt_text
        $targetWord = $this->extractTargetWord($promptText);
        
        if ($targetWord && str_contains($userAnswer, $targetWord)) {
            return 30;
        } else {
            return 10;
        }
    }

    /**
     * Score Word Order exercises (WWO)
     */
    private function scoreWordOrder($userAnswer, $targetAnswer, $promptText): int
    {
        // Extract words from prompt_text (the words to arrange)
        $promptWords = $this->extractWordsFromPrompt($promptText);
        $userWords = explode(' ', $userAnswer);
        
        // Check if all required words are used
        $usedWords = 0;
        foreach ($promptWords as $word) {
            if (in_array($word, $userWords)) {
                $usedWords++;
            }
        }
        
        if ($usedWords === count($promptWords)) {
            return 40;
        } else {
            return 20;
        }
    }

    /**
     * Generic scoring for unknown exercise types
     */
    private function scoreGeneric($userAnswer, $targetAnswer): int
    {
        if ($targetAnswer && $userAnswer === $targetAnswer) {
            return 50;
        } else if ($targetAnswer && $this->calculateSimilarity($userAnswer, $targetAnswer) >= 0.7) {
            return 30;
        } else {
            return 15;
        }
    }

    /**
     * Extract words from sentence building prompt
     */
    private function extractWordsFromPrompt($promptText): array
    {
        // Extract words from "Sắp xếp các từ: word1, word2, word3"
        if (preg_match('/Sắp xếp các từ:\s*(.+)/', $promptText, $matches)) {
            $wordsString = $matches[1];
            $words = array_map('trim', explode(',', $wordsString));
            return array_map('strtolower', $words);
        }
        return [];
    }

    /**
     * Extract target word from prompt
     */
    private function extractTargetWord($promptText): string
    {
        // Extract target word from prompt text
        $words = explode(' ', $promptText);
        return strtolower($words[count($words) - 1] ?? '');
    }

    /**
     * Calculate similarity between user answer and target answer
     */
    private function calculateSimilarity($userAnswer, $targetAnswer): float
    {
        if (empty($targetAnswer)) {
            return 0.0;
        }
        
        $userWords = explode(' ', $userAnswer);
        $targetWords = explode(' ', $targetAnswer);
        
        $matches = 0;
        $totalWords = count($targetWords);
        
        foreach ($targetWords as $targetWord) {
            if (in_array($targetWord, $userWords)) {
                $matches++;
            }
        }
        
        return $totalWords > 0 ? $matches / $totalWords : 0.0;
    }

    /**
     * Calculate spelling score
     */
    private function calculateSpellingScore($userAnswer, $targetAnswer): int
    {
        if (empty($targetAnswer)) {
            return 5; // Default score if no target
        }
        
        $userWords = explode(' ', $userAnswer);
        $targetWords = explode(' ', $targetAnswer);
        
        $correctSpellings = 0;
        $totalWords = count($targetWords);
        
        foreach ($targetWords as $index => $targetWord) {
            if (isset($userWords[$index]) && $userWords[$index] === $targetWord) {
                $correctSpellings++;
            }
        }
        
        if ($totalWords > 0) {
            $accuracy = $correctSpellings / $totalWords;
            return (int)($accuracy * 10);
        }
        
        return 5;
    }
}
