<?php

namespace App\Services;

use App\Models\Question;
use App\Support\Effects;
use App\Services\HighlightBuilder;
use App\Services\ValueValidator;

/**
 * Service for validating user answers against template patterns
 */
class TemplateValidatorService
{
    /**
     * Template configurations for different question types
     */
    private array $templates = [
        'name' => [
            'pattern' => '/^My name is\s+([A-Za-z\s]+)\.?$/i',
            'validation' => 'validateName',
            'feedback_success' => 'Perfect! You introduced yourself correctly. 🌟',
            'feedback_error' => 'Please use the format: "My name is [your name]"',
            'hint' => 'Format: My name is [your name]',
            'scoring' => 'binary'
        ],
        'age' => [
            'pattern' => '/^I am\s+(\d+)\s+years?\s+old\.?$/i',
            'validation' => 'validateAge',
            'feedback_success' => 'Excellent! You stated your age correctly. 👍',
            'feedback_error' => 'Please use: "I am [number] years old"',
            'hint' => 'Format: I am [number] years old'
        ],
        'hobby' => [
            'pattern' => '/^My favorite hobby is\s+(.+)$/i',
            'validation' => 'validateHobby',
            'feedback_success' => 'Great! You described your hobby well. 🎯',
            'feedback_error' => 'Please use: "My favorite hobby is [hobby]"',
            'hint' => 'Format: My favorite hobby is [hobby]'
        ],
        'location' => [
            'pattern' => '/^I live in\s+(.+)$/i',
            'validation' => 'validateLocation',
            'feedback_success' => 'Nice! You told us where you live. 🏠',
            'feedback_error' => 'Please use: "I live in [place]"',
            'hint' => 'Format: I live in [place]'
        ],
        'greeting' => [
            'pattern' => '/^Hello,?\s+([A-Za-z\s]+)$/i',
            'validation' => 'validateName',
            'feedback_success' => 'Perfect greeting! 👋',
            'feedback_error' => 'Please use: "Hello, [name]"',
            'hint' => 'Format: Hello, [name]'
        ],
        'time' => [
            'pattern' => '/^It is\s+(\d{1,2})\s*o\'?clock$/i',
            'validation' => 'validateTime',
            'feedback_success' => 'Correct! You told the time properly. ⏰',
            'feedback_error' => 'Please use: "It is [hour] o\'clock"',
            'hint' => 'Format: It is [hour] o\'clock'
        ],
        'weather' => [
            'pattern' => '/^Today is\s+(sunny|rainy|cloudy|windy|snowy)$/i',
            'validation' => 'validateWeather',
            'feedback_success' => 'Good! You described the weather. ☀️',
            'feedback_error' => 'Please use: "Today is [sunny/rainy/cloudy/windy/snowy]"',
            'hint' => 'Format: Today is [weather]'
        ],
        'word_usage' => [
            'pattern' => '/^(.+)$/',
            'validation' => 'validateWordUsage',
            'feedback_success' => 'Good sentence! You used the word correctly. ✨',
            'feedback_error' => 'Please write a complete sentence using the given word.',
            'hint' => 'Write a complete sentence using the word'
        ]
    ];

    /**
     * Detect question type and evaluate using appropriate template
     */
    public function evaluate(Question $question, string $userAnswer): array
    {
        $questionType = $this->detectQuestionType($question);
        
        if (!$questionType || !isset($this->templates[$questionType])) {
            return $this->genericEvaluation($userAnswer);
        }

        return $this->evaluateWithTemplate($questionType, $userAnswer);
    }

    /**
     * Detect question type based on prompt_text
     */
    private function detectQuestionType(Question $question): ?string
    {
        $prompt = strtolower($question->prompt_text ?? '');
        $detectionMap = [
            'name' => ['what is your name', 'your name'],
            'age' => ['how old', 'your age', 'years old'],
            'hobby' => ['hobby', 'favorite'],
            'location' => ['where', 'live'],
            'greeting' => ['hello', 'greet'],
            'time' => ['time', 'o\'clock'],
            'weather' => ['weather', 'today is'],
        ];

        foreach ($detectionMap as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($prompt, $keyword) !== false) {
                    return $type;
                }
            }
        }

        // Word usage questions (WSG) - short prompt
        if ($question->prompt_text && strlen($question->prompt_text) < 50) {
            return 'word_usage';
        }

        return null;
    }

    /**
     * Evaluate answer using specific template with detailed scoring
     */
    private function evaluateWithTemplate(string $questionType, string $userAnswer): array
    {
        $template = $this->templates[$questionType];
        $trimmedAnswer = trim($userAnswer);
        $extractedValue = null;
        $isValueValid = false;

        if (preg_match($template['pattern'], $trimmedAnswer, $matches)) {
            $extractedValue = trim($matches[1]);
            $isValueValid = $this->validateValue($template['validation'], $extractedValue);

            $highlightMeta = HighlightBuilder::build(
                $questionType,
                $trimmedAnswer,
                $extractedValue,
                $isValueValid
            );

            if ($isValueValid) {
                if (($template['scoring'] ?? 'graded') === 'binary') {
                    return $this->buildBinaryResult($questionType, $extractedValue, $highlightMeta, $template);
                }

                $scoreResult = $this->calculateDetailedScore($questionType, $extractedValue, $trimmedAnswer);
                
                // Check if result has is_correct flag (from error checking)
                $isCorrect = $scoreResult['is_correct'] ?? ($scoreResult['score'] >= 80);
                
                return [
                    'valid' => $isCorrect,
                    'score' => $scoreResult['score'],
                    'feedback' => $scoreResult['feedback'],
                    'template_used' => $questionType,
                    'extracted_value' => $extractedValue,
                    'evaluation_meta' => $highlightMeta,
                    'effect' => Effects::forTemplate($questionType),
                ];
            }

            // Pattern matched but value invalid
            return $this->buildInvalidResult($questionType, $extractedValue, $highlightMeta, $template);
        }

        // Pattern not matched at all
        $highlightMeta = HighlightBuilder::build($questionType, $trimmedAnswer, null, false);
        return $this->buildInvalidResult($questionType, null, $highlightMeta, $template);
    }

    /**
     * Build binary result (correct/incorrect only)
     */
    private function buildBinaryResult(string $questionType, string $extractedValue, array $highlightMeta, array $template): array
    {
        return [
            'valid' => true,
            'score' => null,
            'feedback' => $template['feedback_success'],
            'template_used' => $questionType,
            'extracted_value' => $extractedValue,
            'evaluation_meta' => $highlightMeta,
            'effect' => Effects::forTemplate($questionType),
        ];
    }

    /**
     * Build invalid result
     */
    private function buildInvalidResult(string $questionType, ?string $extractedValue, array $highlightMeta, array $template): array
    {
        return [
            'valid' => false,
            'score' => (($template['scoring'] ?? 'graded') === 'binary') ? null : 0,
            'feedback' => $template['feedback_error'],
            'template_used' => $questionType,
            'extracted_value' => $extractedValue,
            'evaluation_meta' => $highlightMeta,
            'effect' => Effects::forTemplate($questionType),
            'hint' => $template['hint']
        ];
    }

    /**
     * Calculate detailed score based on answer quality
     */
    private function calculateDetailedScore(string $questionType, string $extractedValue, string $userAnswer): array
    {
        $scores = [
            'age' => ['base' => 70, 'age_bonus' => 15, 'punctuation_bonus' => 15],
            'hobby' => ['base' => 60, 'specific_bonus' => 20, 'punctuation_bonus' => 20],
            'location' => ['base' => 60, 'specific_bonus' => 20, 'punctuation_bonus' => 20],
            'greeting' => ['base' => 70, 'capital_bonus' => 15, 'punctuation_bonus' => 15],
            'time' => ['base' => 70, 'time_bonus' => 15, 'punctuation_bonus' => 15],
            'weather' => ['base' => 70, 'weather_bonus' => 15, 'punctuation_bonus' => 15],
            'word_usage' => ['base' => 50, 'length_bonus' => 20, 'punctuation_bonus' => 15, 'capital_bonus' => 15],
        ];

        $config = $scores[$questionType] ?? ['base' => 60, 'bonus' => 20];
        $baseScore = $config['base'];
        $bonusPoints = 0;

        if (!$this->validateValue($this->templates[$questionType]['validation'], $extractedValue)) {
            return ['score' => 0, 'feedback' => $this->templates[$questionType]['feedback_error']];
        }

        // Calculate bonuses based on question type
        switch ($questionType) {
            case 'age':
                $age = (int)$extractedValue;
                if ($age >= 5 && $age <= 100) {
                    $bonusPoints += $config['age_bonus'];
                }
                break;

            case 'hobby':
                $genericHobbies = ['fun', 'good', 'nice', 'cool', 'ok'];
                if (!in_array(strtolower($extractedValue), $genericHobbies)) {
                    $bonusPoints += $config['specific_bonus'];
                }
                
                // Check spelling and grammar errors (similar to word_usage)
                $spellingErrors = $this->checkSpellingErrors($userAnswer);
                $grammarErrors = $this->checkGrammarErrors($userAnswer);
                
                // Re-check grammar with fixed spelling if needed
                if (!empty($spellingErrors)) {
                    $fixedSentence = $this->fixSpellingForGrammarCheck($userAnswer, $spellingErrors);
                    if ($fixedSentence !== $userAnswer) {
                        $additionalErrors = $this->checkGrammarErrors($fixedSentence);
                        $grammarErrors = array_unique(array_merge($grammarErrors, $additionalErrors));
                    }
                }
                
                // Calculate penalties
                $penalty = (count($spellingErrors) * 15) + (count($grammarErrors) * 10);
                $bonusPoints -= $penalty;
                
                // If errors exist, cap score at 70
                if (!empty($spellingErrors) || !empty($grammarErrors)) {
                    $finalScore = min($baseScore + $bonusPoints, 70);
                    $feedback = $this->buildErrorFeedback($finalScore, $spellingErrors, $grammarErrors);
                    
                    return [
                        'score' => max(0, $finalScore),
                        'feedback' => $feedback,
                        'is_correct' => false
                    ];
                }
                break;

            case 'location':
                if (strlen($extractedValue) > 3) {
                    $bonusPoints += $config['specific_bonus'];
                }
                break;

            case 'greeting':
                if (preg_match('/^[A-Z][a-z]+/', $extractedValue)) {
                    $bonusPoints += $config['capital_bonus'];
                }
                break;

            case 'time':
                $time = (int)$extractedValue;
                if ($time >= 1 && $time <= 12) {
                    $bonusPoints += $config['time_bonus'];
                }
                break;

            case 'weather':
                $weatherTypes = ['sunny', 'rainy', 'cloudy', 'windy', 'snowy'];
                if (in_array(strtolower($extractedValue), $weatherTypes)) {
                    $bonusPoints += $config['weather_bonus'];
                }
                break;

            case 'word_usage':
                if (strlen($userAnswer) > 20) {
                    $bonusPoints += $config['length_bonus'];
                }
                if (preg_match('/^[A-Z]/', $userAnswer)) {
                    $bonusPoints += $config['capital_bonus'];
                }
                if (preg_match('/[.!?]$/', $userAnswer)) {
                    $bonusPoints += $config['punctuation_bonus'];
                }
                
                // Check spelling and grammar errors
                $spellingErrors = $this->checkSpellingErrors($userAnswer);
                $grammarErrors = $this->checkGrammarErrors($userAnswer);
                
                // Re-check grammar with fixed spelling if needed
                if (!empty($spellingErrors)) {
                    $fixedSentence = $this->fixSpellingForGrammarCheck($userAnswer, $spellingErrors);
                    if ($fixedSentence !== $userAnswer) {
                        $additionalErrors = $this->checkGrammarErrors($fixedSentence);
                        $grammarErrors = array_unique(array_merge($grammarErrors, $additionalErrors));
                    }
                }
                
                // Calculate penalties
                $penalty = (count($spellingErrors) * 15) + (count($grammarErrors) * 10);
                $bonusPoints -= $penalty;
                
                // If errors exist, cap score at 70
                if (!empty($spellingErrors) || !empty($grammarErrors)) {
                    $finalScore = min($baseScore + $bonusPoints, 70);
                    $feedback = $this->buildErrorFeedback($finalScore, $spellingErrors, $grammarErrors);
                    
                    return [
                        'score' => max(0, $finalScore),
                        'feedback' => $feedback,
                        'is_correct' => false
                    ];
                }
                break;
        }

        // Punctuation bonus (common for all types)
        if (isset($config['punctuation_bonus']) && preg_match('/\.$/', $userAnswer)) {
            $bonusPoints += $config['punctuation_bonus'];
        }

        $totalScore = min(100, $baseScore + $bonusPoints);
        $feedback = $this->templates[$questionType]['feedback_success'];
        
        // Add encouragement based on score
        $encouragements = [
            90 => ' Outstanding work! 🏆',
            80 => ' Well done! 👏',
            70 => ' Good job! 👍',
            60 => ' Almost there! Keep trying! 💪'
        ];

        foreach ($encouragements as $threshold => $message) {
            if ($totalScore >= $threshold) {
                $feedback .= $message;
                break;
            }
        }

        return [
            'score' => $totalScore, 
            'feedback' => $feedback,
            'is_correct' => $totalScore >= 80  // Only correct if score >= 80
        ];
    }

    /**
     * Validate value using validator method
     */
    private function validateValue(string $validatorMethod, string $value): bool
    {
        switch ($validatorMethod) {
            case 'validateName':
                return ValueValidator::validateName($value);
            case 'validateAge':
                return ValueValidator::validateAge($value);
            case 'validateHobby':
                return ValueValidator::validateHobby($value);
            case 'validateLocation':
                return ValueValidator::validateLocation($value);
            case 'validateTime':
                return ValueValidator::validateTime($value);
            case 'validateWeather':
                return ValueValidator::validateWeather($value);
            case 'validateWordUsage':
                return ValueValidator::validateWordUsage($value);
            default:
                return false;
        }
    }

    /**
     * Generic evaluation for non-template questions
     */
    private function genericEvaluation(string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        if (strlen($userAnswer) >= 5) {
            $score += 40;
        } else {
            $reasons[] = "Answer too short!";
        }
        
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 30;
        } else {
            $reasons[] = "Missing punctuation!";
        }
        
        if (preg_match('/^[A-Z]/', $userAnswer)) {
            $score += 30;
        } else {
            $reasons[] = "Missing capitalization!";
        }
        
        $isCorrect = $score >= 60;
        $feedback = $isCorrect ? 
            "Good answer! Keep practicing! 💪" : 
            "Try to write a complete sentence with proper grammar! 📝";
        
        return [
            'valid' => $isCorrect,
            'score' => $score,
            'feedback' => $feedback,
            'template_used' => null,
            'effect' => Effects::forTemplate('generic')
        ];
    }

    /**
     * Get template hint for a question
     */
    public function getTemplateHint(Question $question): ?string
    {
        $questionType = $this->detectQuestionType($question);
        
        if ($questionType && isset($this->templates[$questionType])) {
            return $this->templates[$questionType]['hint'];
        }
        
        return null;
    }

    /**
     * Check if question has a template
     */
    public function hasTemplate(Question $question): bool
    {
        return $this->detectQuestionType($question) !== null;
    }
    
    /**
     * Build error feedback message
     * 
     * @param int $score
     * @param array $spellingErrors
     * @param array $grammarErrors
     * @return string
     */
    private function buildErrorFeedback(int $score, array $spellingErrors, array $grammarErrors): string
    {
        $feedback = "Điểm: {$score}/100 - ";
        $feedback .= $score >= 60 
            ? "Tốt lắm! 👍 Nhưng cần sửa lỗi chính tả và ngữ pháp để đạt điểm cao hơn!"
            : "Cần cố gắng thêm! 💪";
        
        $errorMessages = [];
        if (!empty($spellingErrors)) {
            $errorMessages[] = "Có lỗi chính tả: " . implode(', ', array_slice($spellingErrors, 0, 3));
        }
        if (!empty($grammarErrors)) {
            $errorMessages[] = "Có lỗi ngữ pháp: " . implode(', ', array_slice($grammarErrors, 0, 3));
        }
        
        if (!empty($errorMessages)) {
            $feedback .= " | " . implode(" | ", $errorMessages);
        }
        
        return $feedback;
    }
    
    /**
     * Check spelling errors in a sentence
     * 
     * @param string $sentence
     * @return array Array of misspelled words
     */
    private function checkSpellingErrors(string $sentence): array
    {
        $errors = [];
        $words = preg_split('/\s+/', strtolower($sentence));
        $cleanWords = array_map(fn($word) => preg_replace('/[^a-z]/', '', strtolower($word)), $words);
        $commonWords = $this->getCommonWordsDictionary();
        
        foreach ($cleanWords as $word) {
            if (strlen($word) < 2) continue;
            
            if (in_array($word, $commonWords)) continue;
            
            // Check for typos using Levenshtein distance
            $suggestion = $this->suggestSpellingCorrection($word, $commonWords);
            if ($suggestion && levenshtein($word, $suggestion) <= 2) {
                $errors[] = $word;
                continue;
            }
            
            // Check API if no close match found
            if (!$this->checkWordValidity($word)) {
                $errors[] = $word;
            }
        }
        
        return $errors;
    }
    
    /**
     * Get common words dictionary
     * 
     * @return array
     */
    private function getCommonWordsDictionary(): array
    {
        return [
            'i', 'you', 'he', 'she', 'it', 'we', 'they',
            'am', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
            'have', 'has', 'had', 'do', 'does', 'did', 'done',
            'will', 'would', 'can', 'could', 'should', 'may', 'might',
            'a', 'an', 'the', 'this', 'that', 'these', 'those',
            'my', 'your', 'his', 'her', 'its', 'our', 'their',
            'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from',
            'and', 'or', 'but', 'so', 'because', 'if', 'when', 'where',
            'love', 'like', 'enjoy', 'hate', 'hates', 'want', 'need',
            'sunny', 'rainy', 'cloudy', 'windy', 'snowy', 'weather',
            'really', 'very', 'quite', 'too', 'so', 'much', 'many',
            'today', 'tomorrow', 'yesterday', 'now', 'then',
            'morning', 'afternoon', 'evening', 'night',
            'good', 'bad', 'nice', 'great', 'wonderful', 'beautiful',
            'big', 'small', 'new', 'old', 'young', 'happy', 'sad',
            'day', 'days', 'people',
            'makes', 'make', 'me', 'outside', 'play', 'sun', 'bright',
            'sky', 'blue', 'dog', 'likes', 'window', 'warm', 'went',
            'feel', 'mom', 'wears', 'park', 'garden', 'looks', 'children',
            'drawing', 'pictures', 'beach', 'afternoon', 'sleep', 'under',
            'glasses', 'sunglasses', 'swimming', 'swim', 'pool', 'pools',
            'goes', 'go', 'school', 'schools',
            'spending', 'spend', 'money', 'read', 'reading', 'book', 'books',
            'favorite', 'hobby', 'hobbies', 'playing', 'play', 'game', 'games'
        ];
    }
    
    /**
     * Suggest spelling correction using Levenshtein distance
     * 
     * @param string $word
     * @param array $dictionary
     * @return string|null
     */
    private function suggestSpellingCorrection(string $word, array $dictionary): ?string
    {
        $wordLower = strtolower($word);
        $bestMatch = null;
        $bestDistance = 999;
        
        foreach ($dictionary as $dictWord) {
            $distance = levenshtein($wordLower, $dictWord);
            if ($distance < $bestDistance && $distance <= 2) {
                $bestDistance = $distance;
                $bestMatch = $dictWord;
            }
        }
        
        return $bestMatch;
    }
    
    /**
     * Check if a word is valid using Translation API
     * 
     * @param string $word
     * @return bool
     */
    private function checkWordValidity(string $word): bool
    {
        if (strlen($word) < 2 || strlen($word) > 50) {
            return false;
        }
        
        try {
            $translation = \App\Services\TranslationService::translate($word);
            
            if (in_array($translation['source'] ?? '', ['api', 'dictionary', 'cache'])) {
                $translatedText = $translation['translation'] ?? '';
                
                if (!empty($translatedText) && 
                    $translatedText !== 'Không tìm thấy bản dịch' &&
                    ($translatedText !== $word || preg_match('/[àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđ]/u', $translatedText))) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            return false;
        }
        
        return false;
    }
    
    /**
     * Check grammar errors in a sentence
     * Uses LanguageTool API via GrammarCheckerService if available, falls back to regex patterns
     * 
     * @param string $sentence
     * @return array Array of grammar error descriptions
     */
    private function checkGrammarErrors(string $sentence): array
    {
        $errors = [];
        
        // Try GrammarCheckerService if available (optional dependency)
        // Check if file exists first to avoid autoload errors
        $grammarCheckerPath = app_path('Services/GrammarCheckerService.php');
        if (file_exists($grammarCheckerPath)) {
            try {
                $grammarChecker = new \App\Services\GrammarCheckerService();
                $apiErrors = $grammarChecker->checkGrammar($sentence);
                
                // If API returns errors, add them
                if (!empty($apiErrors)) {
                    $errors = array_merge($errors, $apiErrors);
                }
            } catch (\Throwable $e) {
                // If GrammarCheckerService fails, continue to regex fallback
                \Log::debug('GrammarCheckerService failed, using regex fallback: ' . $e->getMessage());
            }
        }
        
        // Always also check regex patterns as fallback/additional check
        $patterns = config('spellchecker.grammar_patterns', []);
        
        foreach ($patterns as $pattern => $message) {
            if (preg_match($pattern, strtolower($sentence)) && !in_array($message, $errors)) {
                $errors[] = $message;
            }
        }
        
        return $errors;
    }
    
    /**
     * Fix spelling errors temporarily for grammar checking
     * 
     * @param string $sentence
     * @param array $spellingErrors Array of misspelled words
     * @return string Sentence with spelling errors fixed
     */
    private function fixSpellingForGrammarCheck(string $sentence, array $spellingErrors): string
    {
        $fixedSentence = $sentence;
        $corrections = [
            'lve' => 'love', 'lov' => 'love', 'loev' => 'love',
            'realy' => 'really', 'reall' => 'really',
            'weathr' => 'weather', 'weathre' => 'weather',
            'suny' => 'sunny', 'sunnyy' => 'sunny',
        ];
        
        foreach ($spellingErrors as $errorWord) {
            $errorLower = strtolower($errorWord);
            if (isset($corrections[$errorLower])) {
                $fixedSentence = preg_replace(
                    '/\b' . preg_quote($errorWord, '/') . '\b/i',
                    $corrections[$errorLower],
                    $fixedSentence
                );
            } else {
                $suggestion = $this->suggestSpellingCorrection($errorWord, $this->getCommonWordsDictionary());
                if ($suggestion) {
                    $fixedSentence = preg_replace(
                        '/\b' . preg_quote($errorWord, '/') . '\b/i',
                        $suggestion,
                        $fixedSentence
                    );
                }
            }
        }
        
        return $fixedSentence;
    }
}
