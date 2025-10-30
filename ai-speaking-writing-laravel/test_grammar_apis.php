<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST GRAMMAR CHECKING APIs ===\n\n";

$testSentences = [
    'My favorite hobby is spending money.' => 'should be CORRECT',
    'My favorite hobby is speding money.' => 'should detect SPELLING error',
    'She like sunny weather.' => 'should detect GRAMMAR error',
    'I want to swimming.' => 'should detect GRAMMAR error',
];

foreach ($testSentences as $sentence => $expected) {
    echo "─────────────────────────────────────────────────\n";
    echo "Sentence: \"{$sentence}\"\n";
    echo "Expected: {$expected}\n\n";
    
    // Test 1: Ollama
    echo "1️⃣ OLLAMA (Local LLM):\n";
    testOllama($sentence);
    
    // Test 2: Gemini
    echo "\n2️⃣ GEMINI API:\n";
    testGemini($sentence);
    
    // Test 3: LanguageTool
    echo "\n3️⃣ LANGUAGETOOL API:\n";
    testLanguageTool($sentence);
    
    echo "\n";
}

function testOllama($text) {
    $ollamaUrl = config('services.ollama.url', 'http://localhost:11434');
    $ollamaModel = config('services.ollama.model', 'mistral');
    $enabled = config('services.ollama.enabled', false);
    
    if (!$enabled) {
        echo "   ⚠️  Ollama is disabled in .env\n";
        return;
    }
    
    $prompt = "You are an English grammar checker for children learning English. Analyze the following sentence and identify ONLY REAL grammar and spelling errors.

Sentence: \"{$text}\"

Instructions:
1. ONLY identify REAL errors:
   - Spelling mistakes (wrong letters, missing letters)
   - Grammar errors (subject-verb agreement, tense, prepositions, articles)
   - Missing words (like 'is', 'are', 'the', 'a')
   
2. DO NOT mark as errors:
   - Correct gerunds (-ing forms) like 'spending', 'reading', 'playing'
   - Correct hobbies like 'spending money', 'reading books', 'playing games'
   - Correct verb forms
   - Valid English phrases

3. Return ONLY a JSON array of error messages in Vietnamese:
   - Format: [\"Sai ngữ pháp: Chủ ngữ và động từ không khớp.\", \"Có lỗi chính tả: [word].\"]
   - If NO errors, return empty JSON array: []

4. Important:
   - Be strict but accurate
   - Only mark REAL errors, not style preferences
   - Common hobby phrases are VALID (e.g., 'spending money', 'reading books', 'playing sports')
   - Gerunds (-ing) are VALID after 'is' (e.g., 'My hobby is reading' is CORRECT)

Return ONLY valid JSON array:";
    
    try {
        $response = Http::timeout(10)->post("{$ollamaUrl}/api/generate", [
            'model' => $ollamaModel,
            'prompt' => $prompt,
            'stream' => false
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            $content = $data['response'] ?? '';
            
            // Extract JSON
            $jsonMatch = [];
            if (preg_match('/\[.*\]/s', $content, $jsonMatch)) {
                $parsed = json_decode($jsonMatch[0], true);
                if (is_array($parsed)) {
                    echo "   ✅ Errors: " . json_encode($parsed, JSON_UNESCAPED_UNICODE) . "\n";
                    echo "   📝 Raw response: " . substr($content, 0, 100) . "...\n";
                } else {
                    echo "   ❌ Failed to parse JSON\n";
                    echo "   📝 Raw response: " . substr($content, 0, 200) . "\n";
                }
            } else {
                echo "   ❌ No JSON found in response\n";
                echo "   📝 Raw response: " . substr($content, 0, 200) . "\n";
            }
        } else {
            echo "   ❌ API Error: " . $response->status() . "\n";
        }
    } catch (\Throwable $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
}

function testGemini($text) {
    $apiKey = config('services.gemini.api_key');
    $model = config('services.gemini.model', 'gemini-pro');
    
    if (empty($apiKey)) {
        echo "   ⚠️  Gemini API key not configured\n";
        return;
    }
    
    $prompt = "You are an English grammar checker for children learning English. Analyze the following sentence and identify ONLY REAL grammar and spelling errors.

Sentence: \"{$text}\"

Instructions:
1. ONLY identify REAL errors:
   - Spelling mistakes (wrong letters, missing letters)
   - Grammar errors (subject-verb agreement, tense, prepositions, articles)
   - Missing words (like 'is', 'are', 'the', 'a')
   
2. DO NOT mark as errors:
   - Correct gerunds (-ing forms) like 'spending', 'reading', 'playing'
   - Correct hobbies like 'spending money', 'reading books', 'playing games'
   - Correct verb forms
   - Valid English phrases

3. Return ONLY a JSON array of error messages in Vietnamese:
   - Format: [\"Sai ngữ pháp: Chủ ngữ và động từ không khớp.\", \"Có lỗi chính tả: [word].\"]
   - If NO errors, return empty JSON array: []

4. Important:
   - Be strict but accurate
   - Only mark REAL errors, not style preferences
   - Common hobby phrases are VALID (e.g., 'spending money', 'reading books', 'playing sports')
   - Gerunds (-ing) are VALID after 'is' (e.g., 'My hobby is reading' is CORRECT)

Return ONLY valid JSON array:";
    
    try {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        $url .= '?key=' . $apiKey;
        
        $response = Http::timeout(10)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Extract JSON
            $jsonMatch = [];
            if (preg_match('/\[.*\]/s', $content, $jsonMatch)) {
                $parsed = json_decode($jsonMatch[0], true);
                if (is_array($parsed)) {
                    echo "   ✅ Errors: " . json_encode($parsed, JSON_UNESCAPED_UNICODE) . "\n";
                    echo "   📝 Raw response: " . substr($content, 0, 100) . "...\n";
                } else {
                    echo "   ❌ Failed to parse JSON\n";
                    echo "   📝 Raw response: " . substr($content, 0, 200) . "\n";
                }
            } else {
                echo "   ❌ No JSON found in response\n";
                echo "   📝 Raw response: " . substr($content, 0, 200) . "\n";
            }
        } else {
            echo "   ❌ API Error: " . $response->status() . "\n";
            echo "   📝 Response: " . substr($response->body(), 0, 200) . "\n";
        }
    } catch (\Throwable $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
}

function testLanguageTool($text) {
    try {
        $response = Http::asForm()->timeout(10)->post('https://api.languagetool.org/v2/check', [
            'text' => $text,
            'language' => 'en-US'
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            $matches = $data['matches'] ?? [];
            
            if (empty($matches)) {
                echo "   ✅ No errors detected\n";
            } else {
                $errors = [];
                foreach ($matches as $match) {
                    $message = $match['message'] ?? '';
                    $ruleId = $match['rule']['id'] ?? '';
                    $errors[] = "{$message} (Rule: {$ruleId})";
                }
                echo "   ✅ Errors: " . json_encode($errors, JSON_UNESCAPED_UNICODE) . "\n";
            }
        } else {
            echo "   ❌ API Error: " . $response->status() . "\n";
        }
    } catch (\Throwable $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
}

echo "=== END TEST ===\n";

