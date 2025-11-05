<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Support\Effects;

/**
 * Service for scoring using Gemini API
 */
class GeminiScoringService
{
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';
    private const CACHE_TTL = 3600; // 1 hour cache
    
    // Rate limit constants
    private const RATE_LIMIT_PER_MINUTE = 60; // Free tier: 60 requests/minute (based on REQUEST COUNT, not tokens)
    private const RATE_LIMIT_PER_DAY = 1500; // Free tier: 1,500 requests/day (based on REQUEST COUNT, not tokens)
    
    // Token limit for output (higher = allows more output, but doesn't affect rate limit)
    // Model will use only what it needs - this is just the maximum allowed
    private const MAX_OUTPUT_TOKENS = 4096; // Balanced: enough for thoughts + JSON, but not excessive

    /**
     * Evaluate answer using Gemini API
     */
    public function evaluate(Question $question, string $userAnswer): array
    {
        $apiKey = config('services.gemini.api_key');
        // Keep gemini-2.5-flash but with higher token limit to accommodate thoughts
        $model = config('services.gemini.model', 'gemini-2.5-flash');

        if (empty($apiKey)) {
            Log::warning('Gemini API key not configured, falling back to default scoring');
            return $this->getDefaultResult($userAnswer);
        }

        // Ensure question has exercise relationship loaded
        if (!$question->relationLoaded('exercise')) {
            $question->load('exercise.type');
        }

        // Check cache first (avoid API call if cached)
        $cacheKey = 'gemini_score_' . md5($question->id . $userAnswer);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Check rate limits BEFORE making API call
        $rateLimitCheck = $this->checkRateLimit();
        if (!$rateLimitCheck['allowed']) {
            Log::warning('Gemini API rate limit exceeded: ' . $rateLimitCheck['message']);
            return $this->getRateLimitExceededResult($userAnswer, $rateLimitCheck);
        }

        try {
            $prompt = $this->buildPrompt($question, $userAnswer);
            $response = $this->callGeminiAPI($apiKey, $model, $prompt);
            
            // Check for valid response structure
            if ($response && isset($response['candidates'][0])) {
                $candidate = $response['candidates'][0];
                
                // Check if response was truncated
                if (isset($candidate['finishReason']) && $candidate['finishReason'] === 'MAX_TOKENS') {
                    Log::warning('Gemini response truncated due to MAX_TOKENS', [
                        'question_id' => $question->id,
                        'response_structure' => array_keys($response)
                    ]);
                }
                
                // Get text from response
                $responseText = null;
                
                // Check if response has content with parts
                if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts']) && !empty($candidate['content']['parts'])) {
                    $part = $candidate['content']['parts'][0];
                    if (isset($part['text'])) {
                        $responseText = $part['text'];
                    } elseif (is_string($part)) {
                        $responseText = $part;
                    }
                }
                
                if ($responseText && !empty(trim($responseText))) {
                    $result = $this->parseGeminiResponse($responseText, $question, $userAnswer);
                    
                    // Log if we got default result (parsing failed)
                    if ($result['score'] == 70 && $result['feedback'] == 'Good effort! Keep practicing. 💪') {
                        Log::warning('Gemini parsing failed, using default result', [
                            'question_id' => $question->id,
                            'response_text' => substr($responseText, 0, 500),
                            'finish_reason' => $candidate['finishReason'] ?? 'unknown',
                            'has_parts' => isset($candidate['content']['parts']),
                            'parts_count' => isset($candidate['content']['parts']) ? count($candidate['content']['parts']) : 0
                        ]);
                    }
                    
                    Cache::put($cacheKey, $result, self::CACHE_TTL);
                    
                    // Update rate limit counters after successful API call
                    $this->incrementRateLimitCounters();
                    
                    return $result;
                } else {
                    // No text in response - log detailed info
                    Log::warning('Gemini response has no text content', [
                        'question_id' => $question->id,
                        'finish_reason' => $candidate['finishReason'] ?? 'unknown',
                        'has_content' => isset($candidate['content']),
                        'has_parts' => isset($candidate['content']['parts']),
                        'parts_count' => isset($candidate['content']['parts']) ? count($candidate['content']['parts']) : 0,
                        'candidate_structure' => array_keys($candidate)
                    ]);
                }
            }

            Log::warning('Invalid response from Gemini API', [
                'question_id' => $question->id,
                'response_structure' => array_keys($response ?? []),
                'candidates_structure' => isset($response['candidates'][0]) ? array_keys($response['candidates'][0]) : 'no candidates',
                'full_response' => $response
            ]);
            return $this->getDefaultResult($userAnswer);
        } catch (\Throwable $e) {
            Log::error('Gemini API error: ' . $e->getMessage(), [
                'question_id' => $question->id,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultResult($userAnswer);
        }
    }

    /**
     * Build prompt for Gemini API - Optimized for speed (target <10s, but prioritize quality)
     */
    private function buildPrompt(Question $question, string $userAnswer): string
    {
        $promptText = $question->prompt_text ?? '';
        $starterText = $question->starter_text ?? '';
        $targetText = $question->target_text ?? '';
        $questionType = $this->detectQuestionType($question);
        $exerciseTypeCode = $question->exercise->type->code ?? 'WAQ';
        
        // Concise prompt - prioritize speed but ensure quality
        $context = "Evaluate student's English answer. Give detailed Vietnamese feedback.\n\n";
        $context .= "QUESTION: {$promptText}\n";
        if ($starterText) $context .= "STARTER: {$starterText}\n";
        if ($targetText) $context .= "EXPECTED: {$targetText}\n";
        $context .= "TYPE: {$questionType} ({$exerciseTypeCode})\n";
        $context .= "ANSWER: {$userAnswer}\n\n";
        
        $context .= "RULES:\n";
        $context .= "1. Punctuation: ends with '.', '!', '?' → CORRECT\n";
        $context .= "2. Capitalization: first letter uppercase → CORRECT\n";
        $context .= "3. Spelling: report REAL misspellings only\n";
        $context .= "4. Grammar: report REAL errors (e.g., 'I has'→'I have', 'sunny skin'→'sunny skin' OK)\n";
        if ($questionType === 'time') $context .= "5. TIME: 'o'clock' only with whole hours\n\n";
        
        $context .= "CRITICAL: Output ONLY valid JSON. No explanations before/after.\n";
        $context .= "Feedback MUST be DETAILED Vietnamese - include strengths + errors + suggestions.\n\n";
        
        $context .= "JSON format:\n";
        $context .= "{\"score\":<0-100>,\"is_correct\":<true/false>,\"feedback\":\"<Vietnamese>\",\"spelling_errors\":[],\"grammar_errors\":[],\"highlight_segments\":[],\"template_used\":\"{$questionType}\"}\n";
        
        return $context;
    }

    /**
     * Call Gemini API
     */
    private function callGeminiAPI(string $apiKey, string $model, string $prompt): ?array
    {
        $url = sprintf(self::GEMINI_API_URL, $model);
        
        try {
            // Increased timeout to 30s - prioritize quality, allow time for thorough evaluation
            $response = Http::timeout(30)->post($url . '?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topK' => 20,
                    'topP' => 0.9,
                    'maxOutputTokens' => self::MAX_OUTPUT_TOKENS, // Balanced limit - enough for thoughts + JSON
                    'responseMimeType' => 'application/json', // Force JSON response
                ],
                // System instruction - guide model but allow thoughts for reasoning
                'systemInstruction' => [
                    'parts' => [
                        ['text' => 'You are an English grammar evaluator. Think carefully about the answer, then output ONLY valid JSON. The JSON must include: score (0-100), is_correct (true/false), feedback (detailed Vietnamese), spelling_errors (array), grammar_errors (array), highlight_segments (array), and template_used (string).']
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Gemini API error: ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse Gemini response
     */
    private function parseGeminiResponse(string $responseText, Question $question, string $userAnswer): array
    {
        // Try to extract JSON from response
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/', $responseText, $jsonMatch)) {
            try {
                $data = json_decode($jsonMatch[0], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data) && isset($data['score'])) {
                    return $this->formatResult($data, $question, $userAnswer);
                } else {
                    Log::warning('JSON parsed but missing score field', [
                        'question_id' => $question->id,
                        'parsed_data' => $data,
                        'json_error' => json_last_error_msg()
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to parse JSON from Gemini response: ' . $e->getMessage(), [
                    'question_id' => $question->id,
                    'response_preview' => substr($responseText, 0, 200)
                ]);
            }
        }

        // Fallback: try to parse entire response as JSON
        try {
            $data = json_decode($responseText, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data) && isset($data['score'])) {
                return $this->formatResult($data, $question, $userAnswer);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to parse Gemini response as JSON: ' . $e->getMessage(), [
                'question_id' => $question->id,
                'response_preview' => substr($responseText, 0, 200)
            ]);
        }

        // If all parsing fails, return default result
        Log::warning('Gemini response parsing failed completely', [
            'question_id' => $question->id,
            'response_text' => substr($responseText, 0, 500)
        ]);
        return $this->getDefaultResult($userAnswer);
    }

    /**
     * Format result to match expected structure
     */
    private function formatResult(array $data, Question $question, string $userAnswer): array
    {
        $score = isset($data['score']) ? (int)$data['score'] : 70;
        $isCorrect = $data['is_correct'] ?? ($score >= 80);
        $feedback = $data['feedback'] ?? 'Good effort! Keep practicing.';
        $templateUsed = $data['template_used'] ?? $this->detectQuestionType($question);
        $extractedValue = $data['extracted_value'] ?? null;
        
        // Build evaluation meta
        $evaluationMeta = [
            'highlight_segments' => [],
            'notes' => []
        ];

        if (isset($data['highlight_segments']) && is_array($data['highlight_segments'])) {
            $evaluationMeta['highlight_segments'] = $data['highlight_segments'];
        }

        if (isset($data['strengths']) && is_array($data['strengths'])) {
            foreach ($data['strengths'] as $strength) {
                $evaluationMeta['notes'][] = "Điểm mạnh: {$strength}";
            }
        }

        if (isset($data['improvements']) && is_array($data['improvements'])) {
            foreach ($data['improvements'] as $improvement) {
                $evaluationMeta['notes'][] = "Cần cải thiện: {$improvement}";
            }
        }

        // Add spelling errors to notes
        if (isset($data['spelling_errors']) && is_array($data['spelling_errors']) && !empty($data['spelling_errors'])) {
            $evaluationMeta['notes'][] = "Lỗi chính tả: " . implode(', ', $data['spelling_errors']);
        }

        // Add grammar errors to notes
        if (isset($data['grammar_errors']) && is_array($data['grammar_errors']) && !empty($data['grammar_errors'])) {
            $evaluationMeta['notes'][] = "Lỗi ngữ pháp: " . implode(', ', $data['grammar_errors']);
        }

        // Build highlight meta if not provided
        if (empty($evaluationMeta['highlight_segments'])) {
            $evaluationMeta['highlight_segments'] = $this->buildHighlightSegments($userAnswer, $data);
        }

        return [
            'valid' => $isCorrect,
            'score' => $score,
            'feedback' => $feedback,
            'template_used' => $templateUsed,
            'extracted_value' => $extractedValue,
            'evaluation_meta' => $evaluationMeta,
            'effect' => Effects::forTemplate($templateUsed),
        ];
    }

    /**
     * Build highlight segments from user answer
     */
    private function buildHighlightSegments(string $userAnswer, array $data): array
    {
        $segments = [];
        $words = preg_split('/\s+/', $userAnswer);
        
        $spellingErrors = $data['spelling_errors'] ?? [];
        $grammarErrors = $data['grammar_errors'] ?? [];
        
        foreach ($words as $word) {
            $cleanWord = preg_replace('/[^\w]/', '', $word);
            $status = 'correct';
            $message = '';
            
            if (in_array($cleanWord, $spellingErrors)) {
                $status = 'wrong';
                $message = 'Lỗi chính tả';
            }
            
            $segments[] = [
                'text' => $word,
                'status' => $status,
                'message' => $message
            ];
        }
        
        return $segments;
    }

    /**
     * Detect question type
     */
    private function detectQuestionType(Question $question): string
    {
        $prompt = strtolower($question->prompt_text ?? '');
        
        if (strpos($prompt, 'name') !== false) return 'name';
        if (strpos($prompt, 'age') !== false || strpos($prompt, 'old') !== false) return 'age';
        if (strpos($prompt, 'hobby') !== false) return 'hobby';
        if (strpos($prompt, 'live') !== false || strpos($prompt, 'location') !== false) return 'location';
        if (strpos($prompt, 'weather') !== false) return 'weather';
        if (strpos($prompt, 'time') !== false || strpos($prompt, 'clock') !== false) return 'time';
        if ($question->starter_text) return 'word_usage';
        
        return 'general';
    }

    /**
     * Check rate limits for Gemini API
     * Returns array with 'allowed' boolean and 'message' string
     */
    private function checkRateLimit(): array
    {
        $minuteKey = 'gemini_rate_limit_minute_' . date('Y-m-d-H-i');
        $dayKey = 'gemini_rate_limit_day_' . date('Y-m-d');
        
        $minuteCount = Cache::get($minuteKey, 0);
        $dayCount = Cache::get($dayKey, 0);
        
        // Check per-minute limit
        if ($minuteCount >= self::RATE_LIMIT_PER_MINUTE) {
            return [
                'allowed' => false,
                'message' => "Đã vượt quá giới hạn {$minuteCount}/" . self::RATE_LIMIT_PER_MINUTE . " requests/phút. Vui lòng thử lại sau 1 phút.",
                'type' => 'minute',
                'current' => $minuteCount,
                'limit' => self::RATE_LIMIT_PER_MINUTE
            ];
        }
        
        // Check per-day limit
        if ($dayCount >= self::RATE_LIMIT_PER_DAY) {
            return [
                'allowed' => false,
                'message' => "Đã vượt quá giới hạn {$dayCount}/" . self::RATE_LIMIT_PER_DAY . " requests/ngày. Vui lòng thử lại vào ngày mai.",
                'type' => 'day',
                'current' => $dayCount,
                'limit' => self::RATE_LIMIT_PER_DAY
            ];
        }
        
        return [
            'allowed' => true,
            'message' => 'OK',
            'minute_remaining' => self::RATE_LIMIT_PER_MINUTE - $minuteCount,
            'day_remaining' => self::RATE_LIMIT_PER_DAY - $dayCount
        ];
    }

    /**
     * Increment rate limit counters after successful API call
     */
    private function incrementRateLimitCounters(): void
    {
        $minuteKey = 'gemini_rate_limit_minute_' . date('Y-m-d-H-i');
        $dayKey = 'gemini_rate_limit_day_' . date('Y-m-d');
        
        // Increment minute counter (expires in 2 minutes to be safe)
        $currentMinute = Cache::get($minuteKey, 0);
        Cache::put($minuteKey, $currentMinute + 1, 120); // 2 minutes TTL
        
        // Increment day counter (expires in 25 hours to be safe)
        $currentDay = Cache::get($dayKey, 0);
        Cache::put($dayKey, $currentDay + 1, 90000); // 25 hours TTL
    }

    /**
     * Get rate limit exceeded result
     */
    private function getRateLimitExceededResult(string $userAnswer, array $rateLimitInfo): array
    {
        return [
            'valid' => false,
            'score' => 0,
            'feedback' => '⚠️ ' . $rateLimitInfo['message'] . ' Hệ thống đang tạm thời không thể chấm điểm tự động.',
            'template_used' => null,
            'extracted_value' => null,
            'evaluation_meta' => [
                'highlight_segments' => [],
                'notes' => [
                    'Rate limit exceeded',
                    "Current: {$rateLimitInfo['current']}/{$rateLimitInfo['limit']} ({$rateLimitInfo['type']})"
                ]
            ],
            'effect' => Effects::forTemplate('generic'),
        ];
    }

    /**
     * Get rate limit statistics (for monitoring/debugging)
     */
    public function getRateLimitStats(): array
    {
        $minuteKey = 'gemini_rate_limit_minute_' . date('Y-m-d-H-i');
        $dayKey = 'gemini_rate_limit_day_' . date('Y-m-d');
        
        $minuteCount = Cache::get($minuteKey, 0);
        $dayCount = Cache::get($dayKey, 0);
        
        return [
            'minute' => [
                'current' => $minuteCount,
                'limit' => self::RATE_LIMIT_PER_MINUTE,
                'remaining' => self::RATE_LIMIT_PER_MINUTE - $minuteCount,
                'percentage' => round(($minuteCount / self::RATE_LIMIT_PER_MINUTE) * 100, 2)
            ],
            'day' => [
                'current' => $dayCount,
                'limit' => self::RATE_LIMIT_PER_DAY,
                'remaining' => self::RATE_LIMIT_PER_DAY - $dayCount,
                'percentage' => round(($dayCount / self::RATE_LIMIT_PER_DAY) * 100, 2)
            ]
        ];
    }

    /**
     * Get default result when API fails
     */
    private function getDefaultResult(string $userAnswer): array
    {
        return [
            'valid' => true,
            'score' => 70,
            'feedback' => 'Good effort! Keep practicing. 💪',
            'template_used' => 'general',
            'extracted_value' => null,
            'evaluation_meta' => [
                'highlight_segments' => [],
                'notes' => []
            ],
            'effect' => Effects::forTemplate('general'),
        ];
    }
}

