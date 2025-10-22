<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GrammarAnalysisService
{
    private $openaiApiKey;
    private $openaiBaseUrl = 'https://api.openai.com/v1';
    
    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    /**
     * Comprehensive grammar analysis
     */
    public function analyzeGrammar(string $text): array
    {
        $analysis = [
            'overall_score' => 0,
            'grammar_errors' => [],
            'suggestions' => [],
            'strengths' => [],
            'detailed_analysis' => []
        ];

        try {
            // Basic grammar checks
            $basicChecks = $this->performBasicGrammarChecks($text);
            
            // AI-powered grammar analysis
            $aiAnalysis = $this->performAIGrammarAnalysis($text);
            
            // Advanced grammar patterns
            $advancedPatterns = $this->analyzeAdvancedGrammarPatterns($text);
            
            // Combine all analyses
            $analysis = $this->combineGrammarAnalyses($basicChecks, $aiAnalysis, $advancedPatterns);
            
        } catch (\Exception $e) {
            Log::error('Grammar Analysis Error: ' . $e->getMessage());
            $analysis['error'] = 'Grammar analysis temporarily unavailable';
        }

        return $analysis;
    }

    /**
     * Perform basic grammar checks
     */
    private function performBasicGrammarChecks(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $score = 100;

        // Check capitalization
        if (!preg_match('/^[A-Z]/', $text)) {
            $errors[] = 'Sentence should start with a capital letter';
            $score -= 10;
        }

        // Check ending punctuation
        if (!preg_match('/[.!?]$/', $text)) {
            $errors[] = 'Sentence should end with proper punctuation (. ! ?)';
            $score -= 10;
        }

        // Check for double spaces
        if (preg_match('/\s{2,}/', $text)) {
            $errors[] = 'Avoid double spaces';
            $score -= 5;
        }

        // Check for common contractions
        $contractions = ['don\'t', 'won\'t', 'can\'t', 'isn\'t', 'aren\'t', 'wasn\'t', 'weren\'t'];
        foreach ($contractions as $contraction) {
            if (stripos($text, $contraction) !== false) {
                $suggestions[] = "Consider using '{$contraction}' instead of the full form";
            }
        }

        // Check sentence length
        $sentences = $this->splitIntoSentences($text);
        foreach ($sentences as $sentence) {
            $wordCount = str_word_count($sentence);
            if ($wordCount > 30) {
                $errors[] = 'Sentence is too long. Consider breaking it into shorter sentences.';
                $score -= 5;
            } elseif ($wordCount < 3) {
                $errors[] = 'Sentence is too short. Add more details.';
                $score -= 5;
            }
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Perform AI-powered grammar analysis
     */
    private function performAIGrammarAnalysis(string $text): array
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
                        'content' => 'You are an expert English grammar teacher. Analyze the text for grammar errors and provide specific corrections. Respond with JSON format: {"score": number, "errors": [{"type": "error_type", "message": "description", "position": "location"}], "suggestions": ["suggestion1", "suggestion2"], "strengths": ["strength1", "strength2"]}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this text for grammar: \"$text\""
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.2
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '';
                
                $decoded = json_decode($content, true);
                if ($decoded && isset($decoded['score'])) {
                    return $decoded;
                }
            }
        } catch (\Exception $e) {
            Log::error('AI Grammar Analysis Error: ' . $e->getMessage());
        }

        return [
            'score' => 70,
            'errors' => [],
            'suggestions' => ['Consider using AI grammar checking for more detailed analysis'],
            'strengths' => []
        ];
    }

    /**
     * Analyze advanced grammar patterns
     */
    private function analyzeAdvancedGrammarPatterns(string $text): array
    {
        $patterns = [
            'subject_verb_agreement' => $this->checkSubjectVerbAgreement($text),
            'tense_consistency' => $this->checkTenseConsistency($text),
            'pronoun_usage' => $this->checkPronounUsage($text),
            'article_usage' => $this->checkArticleUsage($text),
            'preposition_usage' => $this->checkPrepositionUsage($text),
            'sentence_structure' => $this->checkSentenceStructure($text)
        ];

        $totalScore = 0;
        $allErrors = [];
        $allSuggestions = [];
        $allStrengths = [];

        foreach ($patterns as $pattern => $analysis) {
            $totalScore += $analysis['score'];
            $allErrors = array_merge($allErrors, $analysis['errors']);
            $allSuggestions = array_merge($allSuggestions, $analysis['suggestions']);
            $allStrengths = array_merge($allStrengths, $analysis['strengths']);
        }

        return [
            'score' => round($totalScore / count($patterns)),
            'errors' => $allErrors,
            'suggestions' => $allSuggestions,
            'strengths' => $allStrengths,
            'detailed_patterns' => $patterns
        ];
    }

    /**
     * Check subject-verb agreement
     */
    private function checkSubjectVerbAgreement(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $strengths = [];
        $score = 100;

        // Simple subject-verb agreement checks
        $sentences = $this->splitIntoSentences($text);
        
        foreach ($sentences as $sentence) {
            $words = explode(' ', strtolower(trim($sentence)));
            
            // Check for "is" vs "are"
            if (in_array('is', $words) && in_array('are', $words)) {
                $errors[] = 'Check subject-verb agreement: "is" vs "are"';
                $score -= 10;
            }
            
            // Check for "was" vs "were"
            if (in_array('was', $words) && in_array('were', $words)) {
                $errors[] = 'Check subject-verb agreement: "was" vs "were"';
                $score -= 10;
            }
        }

        if (empty($errors)) {
            $strengths[] = 'Good subject-verb agreement';
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions,
            'strengths' => $strengths
        ];
    }

    /**
     * Check tense consistency
     */
    private function checkTenseConsistency(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $strengths = [];
        $score = 100;

        $words = explode(' ', strtolower($text));
        
        // Check for mixed tenses
        $pastTenses = ['was', 'were', 'had', 'did', 'went', 'came', 'saw'];
        $presentTenses = ['is', 'are', 'am', 'have', 'do', 'go', 'come', 'see'];
        $futureTenses = ['will', 'shall', 'going to'];
        
        $hasPast = !empty(array_intersect($words, $pastTenses));
        $hasPresent = !empty(array_intersect($words, $presentTenses));
        $hasFuture = !empty(array_intersect($words, $futureTenses));
        
        $tenseCount = ($hasPast ? 1 : 0) + ($hasPresent ? 1 : 0) + ($hasFuture ? 1 : 0);
        
        if ($tenseCount > 2) {
            $errors[] = 'Mixed tenses detected. Try to maintain consistent tense throughout';
            $score -= 15;
        }

        if (empty($errors)) {
            $strengths[] = 'Good tense consistency';
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions,
            'strengths' => $strengths
        ];
    }

    /**
     * Check pronoun usage
     */
    private function checkPronounUsage(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $strengths = [];
        $score = 100;

        // Check for common pronoun errors
        if (preg_match('/\b(he|she|it)\s+(is|are)\b/', $text)) {
            $errors[] = 'Check pronoun-verb agreement';
            $score -= 10;
        }

        if (preg_match('/\b(they|we|you)\s+(is|was)\b/', $text)) {
            $errors[] = 'Check pronoun-verb agreement: "they/we/you" should use "are/were"';
            $score -= 10;
        }

        if (empty($errors)) {
            $strengths[] = 'Good pronoun usage';
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions,
            'strengths' => $strengths
        ];
    }

    /**
     * Check article usage
     */
    private function checkArticleUsage(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $strengths = [];
        $score = 100;

        // Check for missing articles
        if (preg_match('/\b(book|car|house|dog|cat)\b/', $text) && !preg_match('/\b(the|a|an)\s+(book|car|house|dog|cat)\b/', $text)) {
            $suggestions[] = 'Consider adding articles (a, an, the) before nouns';
        }

        // Check for double articles
        if (preg_match('/\b(the|a|an)\s+(the|a|an)\b/', $text)) {
            $errors[] = 'Avoid using double articles';
            $score -= 10;
        }

        if (empty($errors)) {
            $strengths[] = 'Good article usage';
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions,
            'strengths' => $strengths
        ];
    }

    /**
     * Check preposition usage
     */
    private function checkPrepositionUsage(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $strengths = [];
        $score = 100;

        // Check for common preposition errors
        $prepositionErrors = [
            '/\bin\s+(morning|afternoon|evening)\b/' => 'Use "in the morning/afternoon/evening"',
            '/\bon\s+(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/' => 'Use "on Monday/Tuesday/etc."',
            '/\bat\s+(home|work|school)\b/' => 'Use "at home/work/school"'
        ];

        foreach ($prepositionErrors as $pattern => $suggestion) {
            if (preg_match($pattern, strtolower($text))) {
                $suggestions[] = $suggestion;
            }
        }

        if (empty($suggestions)) {
            $strengths[] = 'Good preposition usage';
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions,
            'strengths' => $strengths
        ];
    }

    /**
     * Check sentence structure
     */
    private function checkSentenceStructure(string $text): array
    {
        $errors = [];
        $suggestions = [];
        $strengths = [];
        $score = 100;

        $sentences = $this->splitIntoSentences($text);
        
        foreach ($sentences as $sentence) {
            $wordCount = str_word_count($sentence);
            
            if ($wordCount < 3) {
                $errors[] = 'Sentence is too short. Add more details.';
                $score -= 10;
            } elseif ($wordCount > 30) {
                $errors[] = 'Sentence is too long. Consider breaking it into shorter sentences.';
                $score -= 10;
            }
            
            // Check for run-on sentences
            if (preg_match('/\b(and|but|so|because)\b.*\b(and|but|so|because)\b/', $sentence)) {
                $suggestions[] = 'Consider breaking long sentences with multiple conjunctions';
            }
        }

        if (empty($errors)) {
            $strengths[] = 'Good sentence structure';
        }

        return [
            'score' => max(0, $score),
            'errors' => $errors,
            'suggestions' => $suggestions,
            'strengths' => $strengths
        ];
    }

    /**
     * Split text into sentences
     */
    private function splitIntoSentences(string $text): array
    {
        $sentences = preg_split('/[.!?]+/', $text);
        return array_filter(array_map('trim', $sentences), function($sentence) {
            return !empty($sentence);
        });
    }

    /**
     * Combine all grammar analyses
     */
    private function combineGrammarAnalyses(array $basicChecks, array $aiAnalysis, array $advancedPatterns): array
    {
        $overallScore = round(($basicChecks['score'] + $aiAnalysis['score'] + $advancedPatterns['score']) / 3);
        
        $allErrors = array_merge(
            $basicChecks['errors'] ?? [],
            $aiAnalysis['errors'] ?? [],
            $advancedPatterns['errors'] ?? []
        );
        
        $allSuggestions = array_merge(
            $basicChecks['suggestions'] ?? [],
            $aiAnalysis['suggestions'] ?? [],
            $advancedPatterns['suggestions'] ?? []
        );
        
        $allStrengths = array_merge(
            $aiAnalysis['strengths'] ?? [],
            $advancedPatterns['strengths'] ?? []
        );

        return [
            'overall_score' => $overallScore,
            'grammar_errors' => array_unique($allErrors),
            'suggestions' => array_unique($allSuggestions),
            'strengths' => array_unique($allStrengths),
            'detailed_analysis' => [
                'basic_checks' => $basicChecks,
                'ai_analysis' => $aiAnalysis,
                'advanced_patterns' => $advancedPatterns
            ]
        ];
    }

    /**
     * Get grammar improvement tips
     */
    public function getGrammarImprovementTips(): array
    {
        return [
            'subject_verb_agreement' => [
                'tip' => 'Make sure your subject and verb agree in number',
                'example' => 'He is (not He are) going to school'
            ],
            'tense_consistency' => [
                'tip' => 'Keep the same tense throughout your writing',
                'example' => 'I went to the store and bought some milk (not I go to the store and bought some milk)'
            ],
            'pronoun_usage' => [
                'tip' => 'Use pronouns correctly and consistently',
                'example' => 'She is my friend (not Her is my friend)'
            ],
            'article_usage' => [
                'tip' => 'Use a, an, the appropriately',
                'example' => 'I have a cat (not I have cat)'
            ],
            'sentence_structure' => [
                'tip' => 'Write clear, complete sentences',
                'example' => 'I like pizza. (not I like pizza and)'
            ]
        ];
    }
}
