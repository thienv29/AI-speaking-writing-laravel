<?php

namespace App\Services;

use App\Models\WritingAttempt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlagiarismDetectionService
{
    private $openaiApiKey;
    private $openaiBaseUrl = 'https://api.openai.com/v1';
    
    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    /**
     * Detect plagiarism in user's writing
     */
    public function detectPlagiarism(string $userAnswer, int $userId = null): array
    {
        $results = [
            'is_plagiarized' => false,
            'similarity_score' => 0,
            'sources' => [],
            'confidence' => 0,
            'recommendations' => []
        ];

        try {
            // Check against previous attempts by the same user
            if ($userId) {
                $userAttempts = $this->checkAgainstUserHistory($userAnswer, $userId);
                if ($userAttempts['similarity'] > 0.8) {
                    $results['is_plagiarized'] = true;
                    $results['similarity_score'] = $userAttempts['similarity'];
                    $results['sources'][] = 'Previous user attempts';
                    $results['confidence'] = 90;
                }
            }

            // Check against common patterns and templates
            $templateCheck = $this->checkAgainstTemplates($userAnswer);
            if ($templateCheck['similarity'] > 0.7) {
                $results['is_plagiarized'] = true;
                $results['similarity_score'] = max($results['similarity_score'], $templateCheck['similarity']);
                $results['sources'][] = 'Common templates';
                $results['confidence'] = max($results['confidence'], 75);
            }

            // AI-powered plagiarism detection
            $aiDetection = $this->aiPlagiarismDetection($userAnswer);
            if ($aiDetection['is_plagiarized']) {
                $results['is_plagiarized'] = true;
                $results['similarity_score'] = max($results['similarity_score'], $aiDetection['similarity']);
                $results['sources'] = array_merge($results['sources'], $aiDetection['sources']);
                $results['confidence'] = max($results['confidence'], $aiDetection['confidence']);
            }

            // Generate recommendations
            $results['recommendations'] = $this->generatePlagiarismRecommendations($results);

        } catch (\Exception $e) {
            Log::error('Plagiarism Detection Error: ' . $e->getMessage());
            $results['error'] = 'Plagiarism detection temporarily unavailable';
        }

        return $results;
    }

    /**
     * Check against user's previous attempts
     */
    private function checkAgainstUserHistory(string $userAnswer, int $userId): array
    {
        $previousAttempts = WritingAttempt::where('user_id', $userId)
            ->where('is_submitted', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $maxSimilarity = 0;
        $similarAttempts = [];

        foreach ($previousAttempts as $attempt) {
            $similarity = $this->calculateTextSimilarity($userAnswer, $attempt->user_answer);
            
            if ($similarity > 0.6) {
                $maxSimilarity = max($maxSimilarity, $similarity);
                $similarAttempts[] = [
                    'attempt_id' => $attempt->id,
                    'similarity' => $similarity,
                    'created_at' => $attempt->created_at
                ];
            }
        }

        return [
            'similarity' => $maxSimilarity,
            'similar_attempts' => $similarAttempts
        ];
    }

    /**
     * Check against common templates and patterns
     */
    private function checkAgainstTemplates(string $userAnswer): array
    {
        $commonTemplates = [
            'I think that...',
            'In my opinion...',
            'I believe that...',
            'It is important to...',
            'There are many reasons why...',
            'First of all...',
            'Secondly...',
            'In conclusion...',
            'To sum up...',
            'All in all...'
        ];

        $maxSimilarity = 0;
        $matchedTemplates = [];

        foreach ($commonTemplates as $template) {
            $similarity = $this->calculateTextSimilarity($userAnswer, $template);
            
            if ($similarity > 0.5) {
                $maxSimilarity = max($maxSimilarity, $similarity);
                $matchedTemplates[] = [
                    'template' => $template,
                    'similarity' => $similarity
                ];
            }
        }

        return [
            'similarity' => $maxSimilarity,
            'matched_templates' => $matchedTemplates
        ];
    }

    /**
     * AI-powered plagiarism detection
     */
    private function aiPlagiarismDetection(string $userAnswer): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->openaiBaseUrl . '/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert plagiarism detector. Analyze if the text appears to be original or copied from common sources. Respond with JSON format: {"is_plagiarized": boolean, "similarity": number, "sources": ["source1", "source2"], "confidence": number}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this text for plagiarism: \"$userAnswer\""
                    ]
                ],
                'max_tokens' => 200,
                'temperature' => 0.1
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '';
                
                $decoded = json_decode($content, true);
                if ($decoded && isset($decoded['is_plagiarized'])) {
                    return $decoded;
                }
            }
        } catch (\Exception $e) {
            Log::error('AI Plagiarism Detection Error: ' . $e->getMessage());
        }

        return [
            'is_plagiarized' => false,
            'similarity' => 0,
            'sources' => [],
            'confidence' => 0
        ];
    }

    /**
     * Calculate text similarity using multiple algorithms
     */
    private function calculateTextSimilarity(string $text1, string $text2): float
    {
        // Normalize texts
        $text1 = $this->normalizeText($text1);
        $text2 = $this->normalizeText($text2);

        if (empty($text1) || empty($text2)) {
            return 0.0;
        }

        // Exact match
        if ($text1 === $text2) {
            return 1.0;
        }

        // Jaccard similarity
        $jaccardSimilarity = $this->calculateJaccardSimilarity($text1, $text2);
        
        // Levenshtein distance similarity
        $levenshteinSimilarity = $this->calculateLevenshteinSimilarity($text1, $text2);
        
        // Word overlap similarity
        $wordOverlapSimilarity = $this->calculateWordOverlapSimilarity($text1, $text2);

        // Weighted average
        return ($jaccardSimilarity * 0.4) + ($levenshteinSimilarity * 0.3) + ($wordOverlapSimilarity * 0.3);
    }

    /**
     * Calculate Jaccard similarity
     */
    private function calculateJaccardSimilarity(string $text1, string $text2): float
    {
        $words1 = $this->getWordSet($text1);
        $words2 = $this->getWordSet($text2);

        $intersection = array_intersect($words1, $words2);
        $union = array_unique(array_merge($words1, $words2));

        return count($union) > 0 ? count($intersection) / count($union) : 0.0;
    }

    /**
     * Calculate Levenshtein similarity
     */
    private function calculateLevenshteinSimilarity(string $text1, string $text2): float
    {
        $maxLength = max(strlen($text1), strlen($text2));
        if ($maxLength === 0) return 1.0;

        $distance = levenshtein($text1, $text2);
        return 1 - ($distance / $maxLength);
    }

    /**
     * Calculate word overlap similarity
     */
    private function calculateWordOverlapSimilarity(string $text1, string $text2): float
    {
        $words1 = $this->getWordSet($text1);
        $words2 = $this->getWordSet($text2);

        $intersection = array_intersect($words1, $words2);
        $minLength = min(count($words1), count($words2));

        return $minLength > 0 ? count($intersection) / $minLength : 0.0;
    }

    /**
     * Normalize text for comparison
     */
    private function normalizeText(string $text): string
    {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove punctuation
        $text = preg_replace('/[^\w\s]/', '', $text);
        
        return trim($text);
    }

    /**
     * Get word set from text
     */
    private function getWordSet(string $text): array
    {
        $words = explode(' ', $this->normalizeText($text));
        return array_filter($words, function($word) {
            return strlen($word) > 2; // Filter out very short words
        });
    }

    /**
     * Generate plagiarism recommendations
     */
    private function generatePlagiarismRecommendations(array $results): array
    {
        $recommendations = [];

        if ($results['is_plagiarized']) {
            if ($results['similarity_score'] > 0.9) {
                $recommendations[] = 'This appears to be copied from another source. Please write in your own words.';
            } elseif ($results['similarity_score'] > 0.7) {
                $recommendations[] = 'Your answer is very similar to previous attempts. Try to be more original.';
            } else {
                $recommendations[] = 'Consider adding more personal insights and original ideas.';
            }

            if (in_array('Previous user attempts', $results['sources'])) {
                $recommendations[] = 'Avoid repeating the same answers from your previous attempts.';
            }

            if (in_array('Common templates', $results['sources'])) {
                $recommendations[] = 'Try to avoid using common phrases and templates. Be more creative!';
            }
        } else {
            $recommendations[] = 'Great! Your answer appears to be original.';
        }

        return $recommendations;
    }

    /**
     * Get plagiarism statistics for a user
     */
    public function getUserPlagiarismStats(int $userId): array
    {
        $attempts = WritingAttempt::where('user_id', $userId)
            ->where('is_submitted', true)
            ->get();

        $totalAttempts = $attempts->count();
        $plagiarizedAttempts = 0;
        $averageSimilarity = 0;

        foreach ($attempts as $attempt) {
            $plagiarismResult = $this->detectPlagiarism($attempt->user_answer, $userId);
            if ($plagiarismResult['is_plagiarized']) {
                $plagiarizedAttempts++;
            }
            $averageSimilarity += $plagiarismResult['similarity_score'];
        }

        return [
            'total_attempts' => $totalAttempts,
            'plagiarized_attempts' => $plagiarizedAttempts,
            'plagiarism_rate' => $totalAttempts > 0 ? ($plagiarizedAttempts / $totalAttempts) * 100 : 0,
            'average_similarity' => $totalAttempts > 0 ? $averageSimilarity / $totalAttempts : 0
        ];
    }
}
