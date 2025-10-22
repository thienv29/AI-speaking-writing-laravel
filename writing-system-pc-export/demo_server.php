<?php

// Demo server để test Writing System API với Postman
// Chạy: php demo_server.php
// Server: http://localhost:8004

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);

echo "=== Writing System API Demo Server ===\n";
echo "Method: $method\n";
echo "Path: $path\n\n";

// Sample data
$qaExercises = [
    [
        "id" => 1,
        "exercise_id" => 1,
        "title" => "Test Q&A Writing Exercise",
        "question" => "What is your favorite hobby?",
        "instructions" => "Please answer in complete sentences.",
        "sample_answers" => ["My favorite hobby is reading books.", "I enjoy playing football."],
        "expected_word_count" => 15,
        "difficulty" => "easy",
        "order_index" => 1,
        "created_at" => "2025-10-17T09:37:00.000000Z",
        "updated_at" => "2025-10-17T09:37:00.000000Z"
    ]
];

$sentenceBuildingExercises = [
    [
        "id" => 1,
        "exercise_id" => 1,
        "title" => "Test Sentence Building Exercise",
        "target_word" => "beautiful",
        "word_type" => "adjective",
        "word_meaning" => "very attractive or pleasing",
        "word_example" => "She has a beautiful smile.",
        "instructions" => "Write 2 sentences using the word \"beautiful\".",
        "sample_sentences" => ["The sunset is beautiful.", "She wore a beautiful dress."],
        "expected_sentence_count" => 2,
        "expected_word_count" => 8,
        "difficulty" => "medium",
        "order_index" => 1,
        "created_at" => "2025-10-17T09:37:00.000000Z",
        "updated_at" => "2025-10-17T09:37:00.000000Z"
    ]
];

$completeSentenceExercises = [
    [
        "id" => 1,
        "exercise_id" => 1,
        "title" => "Test Complete Sentence Exercise",
        "sentence_start" => "I like to",
        "instructions" => "Complete the sentence with at least 5 words.",
        "hint_words" => ["play", "read", "study", "cook"],
        "sample_completions" => ["I like to play football with my friends.", "I like to read books in the library."],
        "expected_word_count" => 5,
        "sentence_type" => "simple",
        "difficulty" => "easy",
        "order_index" => 1,
        "created_at" => "2025-10-17T09:37:00.000000Z",
        "updated_at" => "2025-10-17T09:37:00.000000Z"
    ]
];

$writingAttempts = [
    [
        "id" => 1,
        "user_id" => 1,
        "writing_exercise_type" => "writing_qa_exercise",
        "writing_exercise_id" => 1,
        "attempt_number" => 1,
        "user_answer" => "My favorite hobby is reading books and playing football.",
        "word_count" => 9,
        "character_count" => 56,
        "time_spent" => 120,
        "is_submitted" => true,
        "is_correct" => true,
        "feedback" => "Good answer! You used the target word correctly.",
        "score" => 85.50,
        "started_at" => "2025-10-17T09:35:00.000000Z",
        "submitted_at" => "2025-10-17T09:37:00.000000Z",
        "created_at" => "2025-10-17T09:35:00.000000Z",
        "updated_at" => "2025-10-17T09:37:00.000000Z"
    ]
];

// Route handling
switch ($path) {
    case '/api/writing/qa-exercises':
        if ($method === 'GET') {
            echo json_encode([
                "success" => true,
                "data" => $qaExercises
            ], JSON_PRETTY_PRINT);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode([
                "success" => true,
                "message" => "Q&A exercise created successfully",
                "data" => array_merge($input, ["id" => 2])
            ], JSON_PRETTY_PRINT);
        }
        break;
        
    case '/api/writing/sentence-building-exercises':
        if ($method === 'GET') {
            echo json_encode([
                "success" => true,
                "data" => $sentenceBuildingExercises
            ], JSON_PRETTY_PRINT);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode([
                "success" => true,
                "message" => "Sentence building exercise created successfully",
                "data" => array_merge($input, ["id" => 2])
            ], JSON_PRETTY_PRINT);
        }
        break;
        
    case '/api/writing/complete-sentence-exercises':
        if ($method === 'GET') {
            echo json_encode([
                "success" => true,
                "data" => $completeSentenceExercises
            ], JSON_PRETTY_PRINT);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode([
                "success" => true,
                "message" => "Complete sentence exercise created successfully",
                "data" => array_merge($input, ["id" => 2])
            ], JSON_PRETTY_PRINT);
        }
        break;
        
    case '/api/writing/complete-sentence-exercises/random':
        if ($method === 'GET') {
            $random = $completeSentenceExercises[array_rand($completeSentenceExercises)];
            echo json_encode([
                "success" => true,
                "data" => $random
            ], JSON_PRETTY_PRINT);
        }
        break;
        
    case '/api/writing/attempts':
        if ($method === 'GET') {
            echo json_encode([
                "success" => true,
                "data" => $writingAttempts
            ], JSON_PRETTY_PRINT);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $wordCount = str_word_count($input['user_answer']);
            $charCount = strlen($input['user_answer']);
            
            echo json_encode([
                "success" => true,
                "message" => "Writing attempt created successfully",
                "data" => array_merge($input, [
                    "id" => 2,
                    "word_count" => $wordCount,
                    "character_count" => $charCount,
                    "attempt_number" => 1,
                    "is_submitted" => false,
                    "is_correct" => false,
                    "score" => null,
                    "started_at" => date('c'),
                    "submitted_at" => null
                ])
            ], JSON_PRETTY_PRINT);
        }
        break;
        
    case '/api/writing/attempts/1/submit':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode([
                "success" => true,
                "message" => "Writing attempt submitted successfully",
                "data" => array_merge($writingAttempts[0], $input, [
                    "submitted_at" => date('c')
                ])
            ], JSON_PRETTY_PRINT);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Endpoint not found",
            "available_endpoints" => [
                "GET /api/writing/qa-exercises",
                "GET /api/writing/sentence-building-exercises", 
                "GET /api/writing/complete-sentence-exercises",
                "GET /api/writing/complete-sentence-exercises/random",
                "GET /api/writing/attempts",
                "POST /api/writing/qa-exercises",
                "POST /api/writing/sentence-building-exercises",
                "POST /api/writing/complete-sentence-exercises",
                "POST /api/writing/attempts",
                "POST /api/writing/attempts/1/submit"
            ]
        ], JSON_PRETTY_PRINT);
        break;
}
