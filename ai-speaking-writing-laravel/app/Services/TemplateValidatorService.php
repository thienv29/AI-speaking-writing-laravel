<?php

namespace App\Services;

use App\Models\Question;
use App\Support\Effects;
use Illuminate\Support\Str;

class TemplateValidatorService
{
    /**
     * Template configurations for different question types
     */
    private $templates = [
        'name' => [
            'pattern' => '/^My name is\s+([A-Za-z\s]+)\.?$/i',
            'validation' => 'validateName',
            'feedback_success' => 'Perfect! You introduced yourself correctly. 🌟',
            'feedback_error' => 'Please use the format: "My name is [your name]"',
            'hint' => 'Format: My name is [your name]',
            // scoring: binary => only correct/incorrect, no numeric score
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
        
        // Name questions
        if (strpos($prompt, 'what is your name') !== false || 
            strpos($prompt, 'your name') !== false) {
            return 'name';
        }
        
        // Age questions
        if (strpos($prompt, 'how old') !== false || 
            strpos($prompt, 'your age') !== false ||
            strpos($prompt, 'years old') !== false) {
            return 'age';
        }
        
        // Hobby questions
        if (strpos($prompt, 'hobby') !== false || 
            strpos($prompt, 'favorite') !== false) {
            return 'hobby';
        }
        
        // Location questions
        if (strpos($prompt, 'where') !== false || 
            strpos($prompt, 'live') !== false) {
            return 'location';
        }
        
        // Greeting questions
        if (strpos($prompt, 'hello') !== false || 
            strpos($prompt, 'greet') !== false) {
            return 'greeting';
        }
        
        // Time questions
        if (strpos($prompt, 'time') !== false || 
            strpos($prompt, 'o\'clock') !== false) {
            return 'time';
        }
        
        // Weather questions
        if (strpos($prompt, 'weather') !== false || 
            strpos($prompt, 'today is') !== false) {
            return 'weather';
        }
        
        // Word usage questions (WSG)
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
            $isValueValid = $this->{$template['validation']}($extractedValue);

            $highlightMeta = $this->buildTemplateHighlightMeta(
                $questionType,
                $trimmedAnswer,
                $extractedValue,
                $isValueValid,
                $template
            );

            if ($isValueValid) {
                if (($template['scoring'] ?? 'graded') === 'binary') {
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

                $scoreResult = $this->calculateDetailedScore($questionType, $extractedValue, $trimmedAnswer);
                return [
                    'valid' => true,
                    'score' => $scoreResult['score'],
                    'feedback' => $scoreResult['feedback'],
                    'template_used' => $questionType,
                    'extracted_value' => $extractedValue,
                    'evaluation_meta' => $highlightMeta,
                    'effect' => Effects::forTemplate($questionType),
                ];
            }

            // Pattern matched but value invalid
            return [
                'valid' => false,
                'score' => (($template['scoring'] ?? 'graded') === 'binary') ? null : 0,
                'feedback' => $template['feedback_error'],
                'template_used' => $questionType,
                'extracted_value' => $extractedValue,
                'evaluation_meta' => $this->buildTemplateHighlightMeta(
                    $questionType,
                    $trimmedAnswer,
                    $extractedValue,
                    false,
                    $template
                ),
                'effect' => Effects::forTemplate($questionType),
                'hint' => $template['hint']
            ];
        }

        // Pattern not matched at all
        return [
            'valid' => false,
            'score' => (($template['scoring'] ?? 'graded') === 'binary') ? null : 0,
            'feedback' => $template['feedback_error'],
            'template_used' => $questionType,
            'extracted_value' => null,
            'evaluation_meta' => $this->buildTemplateHighlightMeta(
                $questionType,
                $trimmedAnswer,
                null,
                false,
                $template
            ),
            'effect' => Effects::forTemplate($questionType),
            'hint' => $template['hint']
        ];
    }

    /**
     * Calculate detailed score based on answer quality
     */
    private function calculateDetailedScore(string $questionType, string $extractedValue, string $userAnswer): array
    {
        $baseScore = 60; // Base score for correct format
        $bonusPoints = 0;
        $feedback = '';

        switch ($questionType) {
            case 'name':
                // 'name' is binary-scored; this branch should not be used when 'scoring' is binary
                if ($this->validateName($extractedValue)) {
                    $baseScore = 100;
                    $feedback = 'Perfect! You introduced yourself correctly. 🌟';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use the format: "My name is [your name]"';
                }
                break;

            case 'age':
                if ($this->validateAge($extractedValue)) {
                    $baseScore = 70;
                    
                    // Bonus for realistic age
                    $age = (int)$extractedValue;
                    if ($age >= 5 && $age <= 100) {
                        $bonusPoints += 15;
                    }
                    
                    // Bonus for complete sentence
                    if (preg_match('/\.$/', $userAnswer)) {
                        $bonusPoints += 15;
                    }
                    
                    $feedback = 'Excellent! You stated your age correctly. 👍';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use: "I am [number] years old"';
                }
                break;

            case 'hobby':
                if ($this->validateHobby($extractedValue)) {
                    $baseScore = 60;
                    
                    // Bonus for specific hobby (not generic)
                    $genericHobbies = ['fun', 'good', 'nice', 'cool', 'ok'];
                    if (!in_array(strtolower($extractedValue), $genericHobbies)) {
                        $bonusPoints += 20;
                    }
                    
                    // Bonus for complete sentence
                    if (preg_match('/\.$/', $userAnswer)) {
                        $bonusPoints += 20;
                    }
                    
                    $feedback = 'Great! You described your hobby well. 🎯';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use: "My favorite hobby is [hobby]"';
                }
                break;

            case 'location':
                if ($this->validateLocation($extractedValue)) {
                    $baseScore = 60;
                    
                    // Bonus for specific location
                    if (strlen($extractedValue) > 3) {
                        $bonusPoints += 20;
                    }
                    
                    // Bonus for complete sentence
                    if (preg_match('/\.$/', $userAnswer)) {
                        $bonusPoints += 20;
                    }
                    
                    $feedback = 'Nice! You told us where you live. 🏠';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use: "I live in [place]"';
                }
                break;

            case 'greeting':
                if ($this->validateName($extractedValue)) {
                    $baseScore = 70;
                    
                    // Bonus for proper capitalization
                    if (preg_match('/^[A-Z][a-z]+/', $extractedValue)) {
                        $bonusPoints += 15;
                    }
                    
                    // Bonus for complete sentence
                    if (preg_match('/\.$/', $userAnswer)) {
                        $bonusPoints += 15;
                    }
                    
                    $feedback = 'Perfect greeting! 👋';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use: "Hello, [name]"';
                }
                break;

            case 'time':
                if ($this->validateTime($extractedValue)) {
                    $baseScore = 70;
                    
                    // Bonus for realistic time
                    $time = (int)$extractedValue;
                    if ($time >= 1 && $time <= 12) {
                        $bonusPoints += 15;
                    }
                    
                    // Bonus for complete sentence
                    if (preg_match('/\.$/', $userAnswer)) {
                        $bonusPoints += 15;
                    }
                    
                    $feedback = 'Great! You told the time correctly. ⏰';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use: "It is [number] o\'clock"';
                }
                break;

            case 'weather':
                if ($this->validateWeather($extractedValue)) {
                    $baseScore = 70;
                    
                    // Bonus for specific weather
                    $weatherTypes = ['sunny', 'rainy', 'cloudy', 'windy', 'snowy'];
                    if (in_array(strtolower($extractedValue), $weatherTypes)) {
                        $bonusPoints += 15;
                    }
                    
                    // Bonus for complete sentence
                    if (preg_match('/\.$/', $userAnswer)) {
                        $bonusPoints += 15;
                    }
                    
                    $feedback = 'Excellent! You described the weather well. 🌤️';
                } else {
                    $baseScore = 0;
                    $feedback = 'Please use: "Today is [sunny/rainy/cloudy/windy/snowy]"';
                }
                break;

            case 'word_usage':
                $baseScore = 50;
                
                // Bonus for sentence length
                if (strlen($userAnswer) > 20) {
                    $bonusPoints += 20;
                }
                
                // Bonus for complete sentence
                if (preg_match('/\.$/', $userAnswer)) {
                    $bonusPoints += 15;
                }
                
                // Bonus for proper capitalization
                if (preg_match('/^[A-Z]/', $userAnswer)) {
                    $bonusPoints += 15;
                }
                
                $feedback = 'Good sentence! You used the word correctly. ✨';
                break;

            default:
                $baseScore = 60;
                $bonusPoints = 20;
                $feedback = 'Good answer! Keep practicing. 💪';
        }

        $totalScore = min(100, $baseScore + $bonusPoints);
        
        // Add encouragement based on score
        if ($totalScore >= 90) {
            $feedback .= ' Outstanding work! 🏆';
        } elseif ($totalScore >= 80) {
            $feedback .= ' Well done! 👏';
        } elseif ($totalScore >= 70) {
            $feedback .= ' Good job! 👍';
        } elseif ($totalScore >= 60) {
            $feedback .= ' Almost there! Keep trying! 💪';
        }

        return [
            'score' => $totalScore,
            'feedback' => $feedback
        ];
    }

    /**
     * Build highlight metadata for templates
     */
    private function buildTemplateHighlightMeta(string $questionType, string $userAnswer, ?string $extractedValue, bool $isValid, array $template): array
    {
        switch ($questionType) {
            case 'name':
                return $this->buildNameHighlightMeta($userAnswer, $isValid);
            case 'age':
                return $this->buildAgeHighlightMeta($userAnswer, $extractedValue, $isValid);
            case 'hobby':
                return $this->buildHobbyHighlightMeta($userAnswer, $extractedValue, $isValid);
            case 'location':
                return $this->buildLocationHighlightMeta($userAnswer, $extractedValue, $isValid);
            case 'greeting':
                return $this->buildGreetingHighlightMeta($userAnswer, $extractedValue, $isValid);
            case 'time':
                return $this->buildTimeHighlightMeta($userAnswer, $extractedValue, $isValid);
            case 'weather':
                return $this->buildWeatherHighlightMeta($userAnswer, $extractedValue, $isValid);
            case 'word_usage':
                return $this->buildWordUsageHighlightMeta($userAnswer, $isValid);
            default:
                return [
                    'highlight_segments' => [],
                    'notes' => []
                ];
        }
    }

    private function buildNameHighlightMeta(string $answer, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $answer = trim($answer);

        if ($answer === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $expectedPrefix = 'my name is';
        $lower = strtolower($answer);
        $prefixLength = strlen($expectedPrefix);
        $rest = '';

        if (strpos($lower, $expectedPrefix) === 0) {
            $prefixText = substr($answer, 0, $prefixLength);
            if ($segment = $this->makeHighlightSegment($prefixText, 'correct')) {
                $segments[] = $segment;
            }
            $rest = substr($answer, $prefixLength);
        } else {
            $pos = stripos($lower, $expectedPrefix);
            if ($pos !== false) {
                $before = substr($answer, 0, $pos);
                if ($segment = $this->makeHighlightSegment($before, 'wrong', 'Câu nên bắt đầu với "My name is".')) {
                    $segments[] = $segment;
                }

                $prefixText = substr($answer, $pos, $prefixLength);
                if ($segment = $this->makeHighlightSegment($prefixText, 'correct')) {
                    $segments[] = $segment;
                }

                $rest = substr($answer, $pos + $prefixLength);
            } else {
                if ($segment = $this->makeHighlightSegment($answer, 'wrong', 'Cần sử dụng cụm "My name is" để giới thiệu tên.')) {
                    $segments[] = $segment;
                }

                return [
                    'highlight_segments' => $segments,
                    'notes' => $notes
                ];
            }
        }

        if ($rest === '') {
            $notes[] = 'Hãy thêm tên sau cụm "My name is".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        if (preg_match('/^\s+/', $rest, $spaceMatch)) {
            $spaceText = $spaceMatch[0];
            if ($segment = $this->makeHighlightSegment($spaceText, 'neutral')) {
                $segments[] = $segment;
            }
            $rest = substr($rest, strlen($spaceText));
        } else {
            $notes[] = 'Thêm khoảng trắng sau cụm "My name is".';
        }

        if ($rest === '') {
            $notes[] = 'Hãy viết tên của bạn sau khoảng trắng.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $punctuation = '';
        if (substr($rest, -1) === '.') {
            $punctuation = '.';
            $nameText = substr($rest, 0, -1);
        } else {
            $nameText = $rest;
            $notes[] = 'Thêm dấu chấm ở cuối câu để hoàn chỉnh câu.';
        }

        $nameTrim = trim($nameText);
        if ($nameTrim === '') {
            if ($segment = $this->makeHighlightSegment($rest, 'wrong', 'Bạn chưa điền tên của mình.')) {
                $segments[] = $segment;
            }

            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $nameValid = $this->validateName($nameTrim);

        if ($segment = $this->makeHighlightSegment(
            $nameText,
            $nameValid ? 'correct' : 'wrong',
            $nameValid ? null : 'Tên chỉ nên chứa chữ cái và khoảng trắng (2-50 ký tự).'
        )) {
            $segments[] = $segment;
        }

        if ($punctuation !== '') {
            if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                $segments[] = $segment;
            }
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildAgeHighlightMeta(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $original = $working;

        $literalI = $this->consumeCaseInsensitiveLiteral($working, 'I');
        if ($literalI === null) {
            if ($segment = $this->makeHighlightSegment($original, 'wrong', 'Câu nên bắt đầu với "I am".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Câu nên bắt đầu bằng "I am".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }
        if ($segment = $this->makeHighlightSegment($literalI, strtoupper($literalI) === 'I' ? 'correct' : 'wrong', strtoupper($literalI) === 'I' ? null : 'Chữ "I" nên được viết hoa.')) {
            $segments[] = $segment;
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng giữa "I" và "am".';
        }

        $literalAm = $this->consumeCaseInsensitiveLiteral($working, 'am');
        if ($literalAm === null) {
            $token = $this->consumeNextTokenSegment($working) ?? '';
            $segmentText = $token !== '' ? $literalI . ($space ?? '') . $token : $working;
            if ($segmentText !== '' && ($segment = $this->makeHighlightSegment($segmentText, 'wrong', 'Sau "I" cần là "am".'))) {
                $segments[] = $segment;
            }
            $notes[] = 'Sau "I" cần là "am".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }
        if ($segment = $this->makeHighlightSegment($literalAm, 'correct')) {
            $segments[] = $segment;
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng sau "am".';
        }

        if ($working === '') {
            $notes[] = 'Hãy viết số tuổi của bạn.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $number = $this->consumeNumberSegment($working);
        if ($number === null) {
            $token = $this->consumeNextTokenSegment($working) ?? '';
            if ($token !== '' && ($segment = $this->makeHighlightSegment($token, 'wrong', 'Tuổi nên được viết bằng số.'))) {
                $segments[] = $segment;
            }
            $notes[] = 'Tuổi nên được viết bằng số trong phạm vi 1-120.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $ageValid = $this->validateAge($number);
        if ($segment = $this->makeHighlightSegment($number, $ageValid ? 'correct' : 'wrong', $ageValid ? null : 'Tuổi nên nằm trong khoảng 1-120.')) {
            $segments[] = $segment;
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng sau số tuổi.';
        }

        $years = $this->consumeCaseInsensitiveLiteral($working, 'years');
        if ($years === null) {
            $years = $this->consumeCaseInsensitiveLiteral($working, 'year');
        }

        if ($years !== null) {
            if ($segment = $this->makeHighlightSegment($years, 'correct')) {
                $segments[] = $segment;
            }
        } else {
            $token = $this->consumeNextTokenSegment($working);
            if ($token !== null && $token !== '') {
                if ($segment = $this->makeHighlightSegment($token, 'wrong', 'Sử dụng "year" hoặc "years" sau số tuổi.')) {
                    $segments[] = $segment;
                }
            } else {
                $notes[] = 'Thêm "years" sau số tuổi.';
            }
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng trước "old".';
        }

        $old = $this->consumeCaseInsensitiveLiteral($working, 'old');
        if ($old !== null) {
            if ($segment = $this->makeHighlightSegment($old, 'correct')) {
                $segments[] = $segment;
            }
        } else {
            $token = $this->consumeNextTokenSegment($working);
            if ($token !== null && $token !== '') {
                if ($segment = $this->makeHighlightSegment($token, 'wrong', 'Câu nên kết thúc bằng từ "old".')) {
                    $segments[] = $segment;
                }
            } else {
                $notes[] = 'Thêm từ "old" để hoàn thiện câu.';
            }
        }

        $punctuation = $this->consumePunctuationSegment($working);
        if ($punctuation !== null) {
            if ($punctuation === '.') {
                if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                    $segments[] = $segment;
                }
            } else {
                if ($segment = $this->makeHighlightSegment($punctuation, 'neutral')) {
                    $segments[] = $segment;
                }
            }
        } else {
            $notes[] = 'Thêm dấu chấm ở cuối câu.';
        }

        // Consume any trailing whitespace
        while (($space = $this->consumeWhitespaceSegment($working)) !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        }

        if (trim($working) !== '') {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Câu có thêm phần không cần thiết sau "old".')) {
                $segments[] = $segment;
            }
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildHobbyHighlightMeta(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $prefix = 'my favorite hobby is';
        if (stripos($working, $prefix) === 0) {
            $literal = substr($working, 0, strlen($prefix));
            if ($segment = $this->makeHighlightSegment($literal, 'correct')) {
                $segments[] = $segment;
            }
            $working = substr($working, strlen($prefix));
        } else {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Câu nên bắt đầu với "My favorite hobby is".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Câu nên bắt đầu với "My favorite hobby is".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng sau "is".';
        }

        if ($working === '') {
            $notes[] = 'Hãy mô tả sở thích của bạn.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $punctuation = '';
        if (substr($working, -1) === '.') {
            $punctuation = '.';
            $working = substr($working, 0, -1);
        }

        $hobbyText = trim($working);
        if ($hobbyText === '') {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Bạn chưa nêu sở thích.')) {
                $segments[] = $segment;
            }
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $hobbyValid = $this->validateHobby($hobbyText);
        if ($segment = $this->makeHighlightSegment($working, $hobbyValid ? 'correct' : 'wrong', $hobbyValid ? null : 'Sở thích nên dài từ 3-100 ký tự.')) {
            $segments[] = $segment;
        }

        if ($punctuation !== '') {
            if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm dấu chấm ở cuối câu.';
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildLocationHighlightMeta(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $prefix = 'i live in';
        if (stripos($working, $prefix) === 0) {
            $literal = substr($working, 0, strlen($prefix));
            if ($segment = $this->makeHighlightSegment($literal, 'correct')) {
                $segments[] = $segment;
            }
            $working = substr($working, strlen($prefix));
        } else {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Câu nên bắt đầu với "I live in".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Câu nên bắt đầu với "I live in".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng sau "in".';
        }

        if ($working === '') {
            $notes[] = 'Hãy cho biết nơi bạn sống.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $punctuation = '';
        if (preg_match('/[.!?]$/', $working, $match)) {
            $punctuation = $match[0];
            $working = substr($working, 0, -strlen($punctuation));
        }

        $locationText = trim($working);
        if ($locationText === '') {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Bạn chưa cung cấp địa điểm.')) {
                $segments[] = $segment;
            }
        } else {
            $locationValid = $this->validateLocation($locationText);
            if ($segment = $this->makeHighlightSegment($working, $locationValid ? 'correct' : 'wrong', $locationValid ? null : 'Địa điểm nên dài 2-100 ký tự.')) {
                $segments[] = $segment;
            }
        }

        if ($punctuation !== '') {
            if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm dấu câu ở cuối câu.';
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildGreetingHighlightMeta(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu chào.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $literalHello = $this->consumeCaseInsensitiveLiteral($working, 'Hello');
        if ($literalHello === null) {
            if ($segment = $this->makeHighlightSegment($answer, 'wrong', 'Câu nên bắt đầu bằng "Hello".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Câu nên bắt đầu bằng "Hello".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }
        if ($segment = $this->makeHighlightSegment($literalHello, 'correct')) {
            $segments[] = $segment;
        }

        $comma = '';
        if (($working !== '') && $working[0] === ',') {
            $comma = ',';
            $working = substr($working, 1);
            if ($segment = $this->makeHighlightSegment($comma, 'correct')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Bạn có thể thêm dấu phẩy sau "Hello," để lịch sự hơn.';
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng trước khi viết tên.';
        }

        if ($working === '') {
            $notes[] = 'Hãy viết tên người bạn chào.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $punctuation = '';
        if (preg_match('/[.!?]$/', $working, $match)) {
            $punctuation = $match[0];
            $working = substr($working, 0, -strlen($punctuation));
        }

        $nameText = trim($working);
        if ($nameText === '') {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Bạn chưa ghi tên.')) {
                $segments[] = $segment;
            }
        } else {
            $nameValid = $this->validateName($nameText);
            if ($segment = $this->makeHighlightSegment($working, $nameValid ? 'correct' : 'wrong', $nameValid ? null : 'Tên chỉ nên chứa chữ cái và khoảng trắng.')) {
                $segments[] = $segment;
            }
        }

        if ($punctuation !== '') {
            if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                $segments[] = $segment;
            }
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildTimeHighlightMeta(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $literalIt = $this->consumeCaseInsensitiveLiteral($working, 'It');
        if ($literalIt === null) {
            if ($segment = $this->makeHighlightSegment($answer, 'wrong', 'Câu nên bắt đầu với "It is".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Câu nên bắt đầu với "It is".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }
        if ($segment = $this->makeHighlightSegment($literalIt, 'correct')) {
            $segments[] = $segment;
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng giữa "It" và "is".';
        }

        $literalIs = $this->consumeCaseInsensitiveLiteral($working, 'is');
        if ($literalIs === null) {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Sau "It" cần là "is".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Sau "It" cần là "is".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }
        if ($segment = $this->makeHighlightSegment($literalIs, 'correct')) {
            $segments[] = $segment;
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng sau "is".';
        }

        if ($working === '') {
            $notes[] = 'Hãy ghi giờ hiện tại bằng số.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $hour = $this->consumeNumberSegment($working);
        if ($hour === null) {
            $token = $this->consumeNextTokenSegment($working) ?? '';
            if ($token !== '' && ($segment = $this->makeHighlightSegment($token, 'wrong', 'Giờ nên được viết bằng số (1-12).'))) {
                $segments[] = $segment;
            }
            $notes[] = 'Giờ nên được viết bằng số từ 1 đến 12.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $hourValid = $this->validateTime($hour);
        if ($segment = $this->makeHighlightSegment($hour, $hourValid ? 'correct' : 'wrong', $hourValid ? null : 'Giờ nên từ 1 đến 12.')) {
            $segments[] = $segment;
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        }

        $oclock = '';
        if (stripos($working, "o'clock") === 0) {
            $oclock = substr($working, 0, strlen("o'clock"));
            $working = substr($working, strlen("o'clock"));
        } elseif (stripos($working, 'oclock') === 0) {
            $oclock = substr($working, 0, strlen('oclock'));
            $working = substr($working, strlen('oclock'));
            $notes[] = 'Thêm dấu nháy trong "o\'clock" để chính xác.';
        }

        if ($oclock !== '') {
            if ($segment = $this->makeHighlightSegment($oclock, 'correct')) {
                $segments[] = $segment;
            }
        } else {
            $token = $this->consumeNextTokenSegment($working);
            if ($token !== null && $token !== '') {
                if ($segment = $this->makeHighlightSegment($token, 'wrong', 'Câu nên kết thúc bằng "o\'clock".')) {
                    $segments[] = $segment;
                }
            }
            $notes[] = 'Câu nên kết thúc bằng "o\'clock".';
        }

        $punctuation = $this->consumePunctuationSegment($working);
        if ($punctuation !== null) {
            if ($punctuation === '.') {
                if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                    $segments[] = $segment;
                }
            } else {
                if ($segment = $this->makeHighlightSegment($punctuation, 'neutral')) {
                    $segments[] = $segment;
                }
            }
        }

        while (($space = $this->consumeWhitespaceSegment($working)) !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        }

        if (trim($working) !== '') {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Câu có phần dư sau "o\'clock".')) {
                $segments[] = $segment;
            }
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildWeatherHighlightMeta(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $prefix = 'today is';
        if (stripos($working, $prefix) === 0) {
            $literal = substr($working, 0, strlen($prefix));
            if ($segment = $this->makeHighlightSegment($literal, 'correct')) {
                $segments[] = $segment;
            }
            $working = substr($working, strlen($prefix));
        } else {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Câu nên bắt đầu với "Today is".')) {
                $segments[] = $segment;
            }
            $notes[] = 'Câu nên bắt đầu với "Today is".';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $space = $this->consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($segment = $this->makeHighlightSegment($space, 'neutral')) {
                $segments[] = $segment;
            }
        } else {
            $notes[] = 'Thêm khoảng trắng sau "is".';
        }

        if ($working === '') {
            $notes[] = 'Hãy mô tả thời tiết (sunny, rainy, ...).';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $punctuation = '';
        if (preg_match('/[.!?]$/', $working, $match)) {
            $punctuation = $match[0];
            $working = substr($working, 0, -strlen($punctuation));
        }

        $weatherWord = trim($working);
        if ($weatherWord === '') {
            if ($segment = $this->makeHighlightSegment($working, 'wrong', 'Bạn chưa ghi loại thời tiết.')) {
                $segments[] = $segment;
            }
        } else {
            $validWeather = $this->validateWeather($weatherWord);
            if ($segment = $this->makeHighlightSegment($working, $validWeather ? 'correct' : 'wrong', $validWeather ? null : 'Dùng một trong các từ: sunny, rainy, cloudy, windy, snowy.')) {
                $segments[] = $segment;
            }
        }

        if ($punctuation !== '') {
            if ($segment = $this->makeHighlightSegment($punctuation, 'correct')) {
                $segments[] = $segment;
            }
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function buildWordUsageHighlightMeta(string $answer, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $trimmed = trim($answer);

        if ($trimmed === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return [
                'highlight_segments' => $segments,
                'notes' => $notes
            ];
        }

        $lengthValid = strlen($trimmed) >= 10;
        $punctuationValid = preg_match('/[.!?]$/', $trimmed) === 1;
        $capitalValid = preg_match('/^[A-Z]/', $trimmed) === 1;

        if (!$lengthValid) {
            $notes[] = 'Câu nên dài ít nhất 10 ký tự.';
        }
        if (!$punctuationValid) {
            $notes[] = 'Thêm dấu câu (., !, ?) ở cuối câu.';
        }
        if (!$capitalValid) {
            $notes[] = 'Viết hoa chữ cái đầu câu.';
        }

        if ($segment = $this->makeHighlightSegment($answer, ($lengthValid && $punctuationValid && $capitalValid && $isValid) ? 'correct' : 'wrong')) {
            $segments[] = $segment;
        }

        return [
            'highlight_segments' => $segments,
            'notes' => $notes
        ];
    }

    private function makeHighlightSegment(string $text, string $status, ?string $message = null): ?array
    {
        if ($text === '') {
            return null;
        }

        return array_filter([
            'text' => $text,
            'status' => $status,
            'message' => $message
        ], function ($value) {
            return $value !== null;
        });
    }

    private function consumeCaseInsensitiveLiteral(string &$input, string $expected): ?string
    {
        $length = strlen($expected);
        if ($length === 0) {
            return '';
        }

        if (strncasecmp($input, $expected, $length) === 0) {
            $actual = substr($input, 0, $length);
            $input = substr($input, $length);
            return $actual;
        }

        return null;
    }

    private function consumeWhitespaceSegment(string &$input): ?string
    {
        if (preg_match('/^\s+/', $input, $match)) {
            $whitespace = $match[0];
            $input = substr($input, strlen($whitespace));
            return $whitespace;
        }

        return null;
    }

    private function consumeNumberSegment(string &$input): ?string
    {
        if (preg_match('/^\d+/', $input, $match)) {
            $number = $match[0];
            $input = substr($input, strlen($number));
            return $number;
        }

        return null;
    }

    private function consumeNextTokenSegment(string &$input): ?string
    {
        if ($input === '') {
            return null;
        }

        if (preg_match('/^[^\s]+/', $input, $match)) {
            $token = $match[0];
            $input = substr($input, strlen($token));
            return $token;
        }

        return null;
    }

    private function consumePunctuationSegment(string &$input): ?string
    {
        if (preg_match('/^[.!?,]/', $input, $match)) {
            $punct = $match[0];
            $input = substr($input, strlen($punct));
            return $punct;
        }

        return null;
    }

    /**
     * Validation methods for different data types
     */
    private function validateName(string $name): bool
    {
        // Name should be 2-50 characters, only letters and spaces
        return strlen($name) >= 2 && 
               strlen($name) <= 50 && 
               preg_match('/^[A-Za-z\s]+$/', $name);
    }

    private function validateAge(string $age): bool
    {
        $ageNum = (int) $age;
        return $ageNum >= 1 && $ageNum <= 120;
    }

    private function validateHobby(string $hobby): bool
    {
        // Hobby should be 3-100 characters
        return strlen($hobby) >= 3 && strlen($hobby) <= 100;
    }

    private function validateLocation(string $location): bool
    {
        // Location should be 2-100 characters
        return strlen($location) >= 2 && strlen($location) <= 100;
    }

    private function validateTime(string $time): bool
    {
        $hour = (int) $time;
        return $hour >= 1 && $hour <= 12;
    }

    private function validateWeather(string $weather): bool
    {
        $validWeather = ['sunny', 'rainy', 'cloudy', 'windy', 'snowy'];
        return in_array(strtolower($weather), $validWeather);
    }

    private function validateWordUsage(string $sentence): bool
    {
        // Basic sentence validation: should be 10+ characters, end with punctuation
        return strlen($sentence) >= 10 && 
               preg_match('/[.!?]$/', $sentence) &&
               preg_match('/^[A-Z]/', $sentence);
    }

    /**
     * Generic evaluation for non-template questions
     */
    private function genericEvaluation(string $userAnswer): array
    {
        $score = 0;
        $reasons = [];
        
        // Basic length check
        if (strlen($userAnswer) >= 5) {
            $score += 40;
        } else {
            $reasons[] = "Answer too short!";
        }
        
        // Grammar check
        if (preg_match('/[.!?]$/', $userAnswer)) {
            $score += 30;
        } else {
            $reasons[] = "Missing punctuation!";
        }
        
        // Capitalization check
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
}
