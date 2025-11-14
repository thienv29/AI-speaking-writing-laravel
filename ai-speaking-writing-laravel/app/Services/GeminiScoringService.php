<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
                    
                    // Check if result is error message (system error)
                    $isErrorResult = $this->isErrorResult($result);
                    
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
                    
                    // Only cache if result is NOT an error
                    if (!$isErrorResult) {
                        Cache::put($cacheKey, $result, self::CACHE_TTL);
                        // Update rate limit counters after successful API call
                        $this->incrementRateLimitCounters();
                    } else {
                        Log::info('Skipping cache for error result', [
                            'question_id' => $question->id,
                            'feedback' => $result['feedback']
                        ]);
                    }
                    
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
            // Don't cache error results
            return $this->getDefaultResult($userAnswer);
        } catch (\Throwable $e) {
            Log::error('Gemini API error: ' . $e->getMessage(), [
                'question_id' => $question->id,
                'trace' => $e->getTraceAsString()
            ]);
            // Don't cache error results
            return $this->getDefaultResult($userAnswer);
        }
    }

    /**
     * Build prompt for Gemini API - Optimized for speed (target <10s, but prioritize quality)
     */
    private function buildPrompt(Question $question, string $userAnswer): string
    {
        $exercise = $question->relationLoaded('exercise')
            ? $question->exercise
            : $question->exercise()->with('type', 'lesson')->first();

        $exerciseType = $exercise && $exercise->relationLoaded('type')
            ? $exercise->type
            : ($exercise ? $exercise->type : null);

        $exerciseTypeCode = strtoupper($exerciseType->code ?? 'WAQ');
        $lessonTitle = '';
        if ($exercise) {
            $lesson = $exercise->relationLoaded('lesson') ? $exercise->lesson : $exercise->lesson()->first();
            $lessonTitle = $lesson->title ?? '';
        }

        $promptText = trim((string) $question->prompt_text);
        $starterText = trim((string) $question->starter_text);
        $targetText = trim((string) $question->target_text);

        $context = "You are a friendly English teacher evaluating the answer of a child (under 13 years old).\n";
        $context .= "Always respond in Vietnamese and always address the child as 'con'. Keep feedback short (2-3 sentences), simple, positive, and encouraging. Do not use complex linguistic terminology.\n\n";

        if (!empty($lessonTitle)) {
            $context .= "LESSON: {$lessonTitle}\n";
        }

        $context .= "QUESTION: " . ($promptText !== '' ? $promptText : '[Không có nội dung câu hỏi]') . "\n";

        if ($starterText !== '') {
            $context .= "STARTER_HINT_SHOWN_TO_CHILD: {$starterText}\n";
        }

        if ($targetText !== '') {
            $context .= "EXAMPLE_ANSWER (optional, chỉ là ví dụ tham khảo, không phải đáp án duy nhất): {$targetText}\n";
        }

        $context .= "CHILD_ANSWER: {$userAnswer}\n";
        $context .= "EXERCISE_TYPE: {$exerciseTypeCode}\n\n";

        switch ($exerciseTypeCode) {
            case 'WCS':
                $context .= "TASK: Sentence completion. The child sees the starter hint above and will finish the sentence in their own words. Accept any natural continuation that keeps the meaning and forms a complete sentence.\n";
                break;
            case 'WSG':
                $context .= "TASK: Sentence building with a required word. The child must write a COMPLETE sentence that includes the given word(s). Mark incorrect if the word is missing or the result is not a full sentence.\n";
                break;
            case 'WAQ':
            default:
                $context .= "TASK: Open question. There is no single correct answer. Judge if the answer is relevant, polite, and meaningful for the question.\n";
                break;
        }

        if (stripos($promptText, 'your name') !== false || stripos($promptText, 'your name?') !== false || stripos($promptText, 'what is your name') !== false) {
            $context .= "This question is about the child's name. Accept any natural response that clearly states the child's name, even if the child explains how they got the name (e.g. 'My parents named me Tony.').\n";
        }

        $context .= "GENERAL EVALUATION GUIDELINES:\n";
        $context .= "- Reward answers that match the topic, make sense, and are written as simple sentences.\n";
        $context .= "- Encourage proper capitalization, but be gentle: mention it only if missing.\n";
        $context .= "- Mark incorrect if the answer is unrelated, empty, or impossible to understand.\n\n";

        $context .= "SCORING (0-100):\n";
        $context .= "- 90-100: Rất tốt – câu trả lời đầy đủ, có ý nghĩa, chính xác.\n";
        $context .= "- 70-89: Tốt – hợp lý, có thể có lỗi nhỏ về chính tả, ngữ pháp, hoặc dấu câu nhưng vẫn hiểu được ý nghĩa.\n";
        $context .= "- 50-69: Tạm được – có lỗi rõ ràng nhưng vẫn liên quan đến câu hỏi và có thể hiểu được.\n";
        $context .= "- 30-49: Yếu – nhiều lỗi hoặc thiếu thông tin quan trọng.\n";
        $context .= "- 0-29: Sai – hoàn toàn không đúng chủ đề, rỗng, hoặc không thể hiểu được.\n\n";
        
        $context .= "FEEDBACK STYLE:\n";
        $context .= "- Luôn bắt đầu bằng lời khen tích cực (ví dụ: 'Con làm tốt lắm!').\n";
        $context .= "- Giải thích ngắn gọn điều cần sửa bằng từ ngữ đơn giản (ví dụ: 'Con nhớ viết hoa chữ cái đầu nhé').\n";
        $context .= "- Kết thúc bằng lời động viên (ví dụ: 'Tiếp tục cố gắng nhé con!').\n";
        $context .= "- Có thể dùng tối đa 1-2 emoji thân thiện.\n";
        $context .= "- Feedback phải hoàn toàn bằng tiếng Việt, không dùng câu tiếng Anh như 'Try saying ...'.\n";
        $context .= "- Không yêu cầu thêm dấu chấm cuối câu trong feedback.\n\n";

        $context .= "OUTPUT REQUIREMENTS:\n";
        $context .= "- Chỉ trả về JSON hợp lệ, không thêm lời giải thích trước hoặc sau.\n";
        $context .= "- Cấu trúc JSON: {\"score\":<0-100>,\"is_correct\":<true/false>,\"feedback\":\"<Vietnamese>\",\"spelling_errors\":[],\"grammar_errors\":[],\"highlight_segments\":[],\"template_used\":\"" . strtolower($exerciseTypeCode) . "\"}.\n";
        
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
                // System instruction - guide model for kid-friendly feedback
                'systemInstruction' => [
                    'parts' => [
                        ['text' => 'You are a friendly English teacher for children under 13. Always address the child as "con" (never "bạn" or "em"). Give simple, short, encouraging feedback in Vietnamese. Use simple words, avoid complex grammar terms, and be positive. Output ONLY valid JSON with: score (0-100), is_correct (true/false), feedback (simple Vietnamese, 2-3 sentences max, always use "con"), spelling_errors (array), grammar_errors (array), highlight_segments (array), and template_used (string).']
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
        $score = isset($data['score']) ? (int) $data['score'] : 30;
        // Ensure score is valid (0-100)
        $score = max(0, min(100, $score));
        
        // Làm tròn lên 100 nếu điểm > 90
        if ($score > 90) {
            $score = 100;
        }
        
        $isCorrect = $data['is_correct'] ?? ($score >= 80);
        $feedback = $data['feedback'] ?? 'Vui lòng kiểm tra lại câu trả lời của con nhé.';

        $exercise = $question->relationLoaded('exercise')
            ? $question->exercise
            : $question->exercise()->with('type')->first();

        $exerciseTypeCode = strtoupper(optional(optional($exercise)->type)->code ?? 'GENERAL');
        $templateUsed = strtolower($data['template_used'] ?? $exerciseTypeCode);
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
            'feedback' => '⚠️ ' . $rateLimitInfo['message'] . ' Hệ thống đang tạm thời không thể chấm điểm tự động. Con thử lại sau nhé.',
            'template_used' => null,
            'extracted_value' => null,
            'evaluation_meta' => [
                'highlight_segments' => [],
                'notes' => [
                    'Rate limit exceeded',
                    "Current: {$rateLimitInfo['current']}/{$rateLimitInfo['limit']} ({$rateLimitInfo['type']})"
                ]
            ],
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
     * Check if result is an error result (system error)
     */
    private function isErrorResult(array $result): bool
    {
        // Check if feedback contains error message
        $feedback = $result['feedback'] ?? '';
        $errorKeywords = [
            'hệ thống gặp sự cố',
            'gặp sự cố khi chấm bài',
            'hệ thống đang tạm thời không thể chấm điểm'
        ];
        
        foreach ($errorKeywords as $keyword) {
            if (stripos($feedback, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get default result when API fails
     */
    private function getDefaultResult(string $userAnswer): array
    {
        // Return low score when API fails - indicates system error, not student success
        return [
            'valid' => true,
            'score' => 30,
            'feedback' => 'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.',
            'template_used' => 'general',
            'extracted_value' => null,
            'evaluation_meta' => [
                'highlight_segments' => [],
                'notes' => []
            ],
        ];
    }
}

