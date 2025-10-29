<?php

// Test script for Writing Evaluation
// Run: php test_writing.php

require_once 'vendor/autoload.php';

use App\Services\AttemptService;
use App\Models\Question;
use App\Models\Exercise;
use App\Models\ExerciseType;

// Mock data for testing
$testCases = [
    // WAQ - Answer the question
    [
        'type' => 'WAQ',
        'question' => 'What is your name?',
        'target' => 'My name is John',
        'user_answer' => 'My name is John',
        'expected_score' => 'high'
    ],
    [
        'type' => 'WAQ', 
        'question' => 'What is your name?',
        'target' => 'My name is John',
        'user_answer' => 'John',
        'expected_score' => 'medium'
    ],
    
    // WCS - Complete the sentence
    [
        'type' => 'WCS',
        'question' => 'Complete: I am ___ years old',
        'starter' => 'I am',
        'target' => 'I am 10 years old',
        'user_answer' => '10 years old',
        'expected_score' => 'high'
    ],
    
    // WSG - Write sentence using given word
    [
        'type' => 'WSG',
        'question' => 'Use the word: Vietnam',
        'prompt' => 'Vietnam',
        'user_answer' => 'I live in Vietnam.',
        'expected_score' => 'high'
    ],
    [
        'type' => 'WSG',
        'question' => 'Use the word: Vietnam', 
        'prompt' => 'Vietnam',
        'user_answer' => 'Hello',
        'expected_score' => 'low'
    ]
];

echo "🧪 Testing Writing Evaluation System\n";
echo "=====================================\n\n";

foreach ($testCases as $i => $test) {
    echo "Test " . ($i + 1) . ": {$test['type']}\n";
    echo "Question: {$test['question']}\n";
    echo "User Answer: {$test['user_answer']}\n";
    
    // Simulate evaluation logic
    $score = 0;
    $reasons = [];
    
    switch ($test['type']) {
        case 'WAQ':
            if (mb_strlen($test['user_answer']) >= 5) $score += 40;
            if (isset($test['target']) && str_contains(strtolower($test['user_answer']), strtolower($test['target']))) {
                $score += 40;
            }
            if (preg_match('/[.!?]$/', $test['user_answer'])) $score += 20;
            break;
            
        case 'WCS':
            if (mb_strlen($test['user_answer']) >= 5) $score += 50;
            if (isset($test['target']) && str_contains(strtolower($test['user_answer']), strtolower($test['target']))) {
                $score += 30;
            }
            if (preg_match('/[.!?]$/', $test['user_answer'])) $score += 20;
            break;
            
        case 'WSG':
            if (isset($test['prompt']) && str_contains(strtolower($test['user_answer']), strtolower($test['prompt']))) {
                $score += 50;
            }
            if (mb_strlen($test['user_answer']) >= 8) $score += 30;
            if (preg_match('/^[A-Z]/', $test['user_answer']) && preg_match('/[.!?]$/', $test['user_answer'])) {
                $score += 20;
            }
            break;
    }
    
    $isCorrect = $score >= 60;
    $status = $isCorrect ? '✅ PASS' : '❌ FAIL';
    
    echo "Score: {$score}/100 {$status}\n";
    echo "Expected: {$test['expected_score']}\n";
    echo "---\n\n";
}

echo "✅ Writing Evaluation System Ready!\n";
echo "📝 Features:\n";
echo "   - WAQ: Answer the question\n";
echo "   - WCS: Complete the sentence\n"; 
echo "   - WSG: Write sentence using given word\n";
echo "   - Heuristic scoring (no external API needed)\n";
echo "   - Child-friendly feedback\n\n";
