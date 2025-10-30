<?php

namespace App\Services;

use App\Services\TokenConsumer;
use App\Services\ValueValidator;
use App\Services\SpellChecker;

/**
 * Base helper for highlight building operations
 */
trait HighlightHelper
{
    protected static function makeSegment(string $text, string $status, ?string $message = null): ?array
    {
        if ($text === '') {
            return null;
        }
        return array_filter([
            'text' => $text,
            'status' => $status,
            'message' => $message
        ], fn($value) => $value !== null);
    }

    protected static function checkEmpty(string $answer, string &$working, array &$notes): bool
    {
        $working = trim($answer);
        if ($working === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return true;
        }
        return false;
    }

    protected static function consumePrefix(string &$working, string $prefix, array &$segments, array &$notes, string $errorMsg): bool
    {
        $lower = strtolower($working);
        $prefixLength = strlen($prefix);
        
        if (stripos($lower, $prefix) === 0) {
            $prefixText = substr($working, 0, $prefixLength);
            if ($seg = self::makeSegment($prefixText, 'correct')) {
                $segments[] = $seg;
            }
            $working = substr($working, $prefixLength);
            return true;
        }
        
        $pos = stripos($lower, $prefix);
        if ($pos !== false) {
            $before = substr($working, 0, $pos);
            if ($seg = self::makeSegment($before, 'wrong', $errorMsg)) {
                $segments[] = $seg;
            }
            $prefixText = substr($working, $pos, $prefixLength);
            if ($seg = self::makeSegment($prefixText, 'correct')) {
                $segments[] = $seg;
            }
            $working = substr($working, $pos + $prefixLength);
            return true;
        }
        
        if ($seg = self::makeSegment($working, 'wrong', $errorMsg)) {
            $segments[] = $seg;
        }
        $notes[] = $errorMsg;
        return false;
    }

    protected static function consumeWhitespace(string &$working, array &$segments, array &$notes, string $errorMsg): void
    {
        $space = TokenConsumer::consumeWhitespaceSegment($working);
        if ($space !== null) {
            if ($seg = self::makeSegment($space, 'neutral')) {
                $segments[] = $seg;
            }
        } else {
            $notes[] = $errorMsg;
        }
    }

    protected static function consumePunctuation(string &$working, array &$segments, array &$notes, string $errorMsg): ?string
    {
        $punctuation = TokenConsumer::consumePunctuationSegment($working);
        if ($punctuation !== null) {
            if ($punctuation === '.') {
                if ($seg = self::makeSegment($punctuation, 'correct')) {
                    $segments[] = $seg;
                }
            } else {
                if ($seg = self::makeSegment($punctuation, 'neutral')) {
                    $segments[] = $seg;
                }
            }
            return $punctuation;
        }
        $notes[] = $errorMsg;
        return null;
    }

    protected static function validateAndSegment(string &$working, callable $validator, string $errorMsg, array &$segments, array &$notes): bool
    {
        $value = trim($working);
        if ($value === '') {
            return false;
        }
        
        $isValid = $validator($value);
        if ($seg = self::makeSegment($working, $isValid ? 'correct' : 'wrong', $isValid ? null : $errorMsg)) {
            $segments[] = $seg;
        }
        return $isValid;
    }
}

/**
 * Build highlight metadata for template validation
 */
class HighlightBuilder
{
    use HighlightHelper;

    public static function build(string $questionType, string $userAnswer, ?string $extractedValue, bool $isValid): array
    {
        switch ($questionType) {
            case 'name':
                return self::buildName($userAnswer, $extractedValue, $isValid);
            case 'age':
                return self::buildAge($userAnswer, $extractedValue, $isValid);
            case 'hobby':
                return self::buildHobby($userAnswer, $extractedValue, $isValid);
            case 'location':
                return self::buildLocation($userAnswer, $extractedValue, $isValid);
            case 'greeting':
                return self::buildGreeting($userAnswer, $extractedValue, $isValid);
            case 'time':
                return self::buildTime($userAnswer, $extractedValue, $isValid);
            case 'weather':
                return self::buildWeather($userAnswer, $extractedValue, $isValid);
            case 'word_usage':
                return self::buildWordUsage($userAnswer, $extractedValue, $isValid);
            default:
                return ['highlight_segments' => [], 'notes' => []];
        }
    }

    private static function buildName(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        if (!self::consumePrefix($working, 'my name is', $segments, $notes, 'Câu nên bắt đầu với "My name is".')) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        if ($working === '') {
            $notes[] = 'Hãy thêm tên sau cụm "My name is".';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau cụm "My name is".');

        if ($working === '') {
            $notes[] = 'Hãy viết tên của bạn sau khoảng trắng.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        // Extract punctuation
        $punctuation = '';
        if (substr($working, -1) === '.') {
            $punctuation = '.';
            $working = substr($working, 0, -1);
        } else {
            $notes[] = 'Thêm dấu chấm ở cuối câu để hoàn chỉnh câu.';
        }

        $nameTrim = trim($working);
        if ($nameTrim === '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Bạn chưa điền tên của mình.')) {
                $segments[] = $seg;
            }
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $nameValid = ValueValidator::validateName($nameTrim);
        if ($seg = self::makeSegment($working, $nameValid ? 'correct' : 'wrong', $nameValid ? null : 'Tên chỉ nên chứa chữ cái và khoảng trắng (2-50 ký tự).')) {
            $segments[] = $seg;
        }

        if ($punctuation !== '') {
            if ($seg = self::makeSegment($punctuation, 'correct')) {
                $segments[] = $seg;
            }
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildAge(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        // "I"
        $literalI = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'I');
        if ($literalI === null) {
            if ($seg = self::makeSegment($answer, 'wrong', 'Câu nên bắt đầu với "I am".')) {
                $segments[] = $seg;
            }
            $notes[] = 'Câu nên bắt đầu bằng "I am".';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }
        if ($seg = self::makeSegment($literalI, strtoupper($literalI) === 'I' ? 'correct' : 'wrong', strtoupper($literalI) === 'I' ? null : 'Chữ "I" nên được viết hoa.')) {
            $segments[] = $seg;
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng giữa "I" và "am".');

        // "am"
        $literalAm = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'am');
        if ($literalAm === null) {
            $token = TokenConsumer::consumeNextTokenSegment($working) ?? '';
            $segmentText = $token !== '' ? $literalI . ' ' . $token : $working;
            if ($segmentText !== '' && ($seg = self::makeSegment($segmentText, 'wrong', 'Sau "I" cần là "am".'))) {
                $segments[] = $seg;
            }
            $notes[] = 'Sau "I" cần là "am".';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }
        if ($seg = self::makeSegment($literalAm, 'correct')) {
            $segments[] = $seg;
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau "am".');

        if ($working === '') {
            $notes[] = 'Hãy viết số tuổi của bạn.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        // Number
        $number = TokenConsumer::consumeNumberSegment($working);
        if ($number === null) {
            $token = TokenConsumer::consumeNextTokenSegment($working) ?? '';
            if ($token !== '' && ($seg = self::makeSegment($token, 'wrong', 'Tuổi nên được viết bằng số.'))) {
                $segments[] = $seg;
            }
            $notes[] = 'Tuổi nên được viết bằng số trong phạm vi 1-120.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $ageValid = ValueValidator::validateAge($number);
        if ($seg = self::makeSegment($number, $ageValid ? 'correct' : 'wrong', $ageValid ? null : 'Tuổi nên nằm trong khoảng 1-120.')) {
            $segments[] = $seg;
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau số tuổi.');

        // "years" or "year"
        $years = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'years');
        if ($years === null) {
            $years = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'year');
        }

        if ($years !== null) {
            if ($seg = self::makeSegment($years, 'correct')) {
                $segments[] = $seg;
            }
        } else {
            $token = TokenConsumer::consumeNextTokenSegment($working);
            if ($token !== null && $token !== '') {
                if ($seg = self::makeSegment($token, 'wrong', 'Sử dụng "year" hoặc "years" sau số tuổi.')) {
                    $segments[] = $seg;
                }
            } else {
                $notes[] = 'Thêm "years" sau số tuổi.';
            }
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng trước "old".');

        // "old"
        $old = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'old');
        if ($old !== null) {
            if ($seg = self::makeSegment($old, 'correct')) {
                $segments[] = $seg;
            }
        } else {
            $token = TokenConsumer::consumeNextTokenSegment($working);
            if ($token !== null && $token !== '') {
                if ($seg = self::makeSegment($token, 'wrong', 'Câu nên kết thúc bằng từ "old".')) {
                    $segments[] = $seg;
                }
            } else {
                $notes[] = 'Thêm từ "old" để hoàn thiện câu.';
            }
        }

        self::consumePunctuation($working, $segments, $notes, 'Thêm dấu chấm ở cuối câu.');

        // Consume trailing whitespace
        while (($space = TokenConsumer::consumeWhitespaceSegment($working)) !== null) {
            if ($seg = self::makeSegment($space, 'neutral')) {
                $segments[] = $seg;
            }
        }

        if (trim($working) !== '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Câu có thêm phần không cần thiết sau "old".')) {
                $segments[] = $seg;
            }
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildHobby(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        if (!self::consumePrefix($working, 'my favorite hobby is', $segments, $notes, 'Câu nên bắt đầu với "My favorite hobby is".')) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau "is".');

        if ($working === '') {
            $notes[] = 'Hãy mô tả sở thích của bạn.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $punctuation = '';
        if (substr($working, -1) === '.') {
            $punctuation = '.';
            $working = substr($working, 0, -1);
        }

        $hobbyText = trim($working);
        if ($hobbyText === '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Bạn chưa nêu sở thích.')) {
                $segments[] = $seg;
            }
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        // Check spelling and get suggestions
        $spellCheck = SpellChecker::checkHobbySpelling($hobbyText);
        $hobbyValid = $spellCheck['isValid'];
        
        // Add spelling suggestions to notes
        if (!empty($spellCheck['errors'])) {
            foreach ($spellCheck['errors'] as $error) {
                $notes[] = $error['message'];
            }
        }
        
        if ($seg = self::makeSegment($working, $hobbyValid ? 'correct' : 'wrong', $hobbyValid ? null : 'Sở thích không hợp lý hoặc có lỗi chính tả.')) {
            $segments[] = $seg;
        }

        if ($punctuation !== '') {
            if ($seg = self::makeSegment($punctuation, 'correct')) {
                $segments[] = $seg;
            }
        } else {
            $notes[] = 'Thêm dấu chấm ở cuối câu.';
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildLocation(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        if (!self::consumePrefix($working, 'i live in', $segments, $notes, 'Câu nên bắt đầu với "I live in".')) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau "in".');

        if ($working === '') {
            $notes[] = 'Hãy cho biết nơi bạn sống.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $punctuation = '';
        if (preg_match('/[.!?]$/', $working, $match)) {
            $punctuation = $match[0];
            $working = substr($working, 0, -strlen($punctuation));
        }

        $locationText = trim($working);
        if ($locationText === '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Bạn chưa cung cấp địa điểm.')) {
                $segments[] = $seg;
            }
        } else {
            $locationValid = ValueValidator::validateLocation($locationText);
            if ($seg = self::makeSegment($working, $locationValid ? 'correct' : 'wrong', $locationValid ? null : 'Địa điểm nên dài 2-100 ký tự.')) {
                $segments[] = $seg;
            }
        }

        if ($punctuation !== '') {
            if ($seg = self::makeSegment($punctuation, 'correct')) {
                $segments[] = $seg;
            }
        } else {
            $notes[] = 'Thêm dấu câu ở cuối câu.';
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildGreeting(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $literalHello = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'Hello');
        if ($literalHello === null) {
            if ($seg = self::makeSegment($answer, 'wrong', 'Câu nên bắt đầu bằng "Hello".')) {
                $segments[] = $seg;
            }
            $notes[] = 'Câu nên bắt đầu bằng "Hello".';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }
        if ($seg = self::makeSegment($literalHello, 'correct')) {
            $segments[] = $seg;
        }

        // Comma (optional)
        $comma = '';
        if (($working !== '') && $working[0] === ',') {
            $comma = ',';
            $working = substr($working, 1);
            if ($seg = self::makeSegment($comma, 'correct')) {
                $segments[] = $seg;
            }
        } else {
            $notes[] = 'Bạn có thể thêm dấu phẩy sau "Hello," để lịch sự hơn.';
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng trước khi viết tên.');

        if ($working === '') {
            $notes[] = 'Hãy viết tên người bạn chào.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $punctuation = '';
        if (preg_match('/[.!?]$/', $working, $match)) {
            $punctuation = $match[0];
            $working = substr($working, 0, -strlen($punctuation));
        }

        $nameText = trim($working);
        if ($nameText === '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Bạn chưa ghi tên.')) {
                $segments[] = $seg;
            }
        } else {
            $nameValid = ValueValidator::validateName($nameText);
            if ($seg = self::makeSegment($working, $nameValid ? 'correct' : 'wrong', $nameValid ? null : 'Tên chỉ nên chứa chữ cái và khoảng trắng.')) {
                $segments[] = $seg;
            }
        }

        if ($punctuation !== '') {
            if ($seg = self::makeSegment($punctuation, 'correct')) {
                $segments[] = $seg;
            }
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildTime(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $literalIt = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'It');
        if ($literalIt === null) {
            if ($seg = self::makeSegment($answer, 'wrong', 'Câu nên bắt đầu với "It is".')) {
                $segments[] = $seg;
            }
            $notes[] = 'Câu nên bắt đầu với "It is".';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }
        if ($seg = self::makeSegment($literalIt, 'correct')) {
            $segments[] = $seg;
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng giữa "It" và "is".');

        $literalIs = TokenConsumer::consumeCaseInsensitiveLiteral($working, 'is');
        if ($literalIs === null) {
            if ($seg = self::makeSegment($working, 'wrong', 'Sau "It" cần là "is".')) {
                $segments[] = $seg;
            }
            $notes[] = 'Sau "It" cần là "is".';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }
        if ($seg = self::makeSegment($literalIs, 'correct')) {
            $segments[] = $seg;
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau "is".');

        if ($working === '') {
            $notes[] = 'Hãy ghi giờ hiện tại bằng số.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $hour = TokenConsumer::consumeNumberSegment($working);
        if ($hour === null) {
            $token = TokenConsumer::consumeNextTokenSegment($working) ?? '';
            if ($token !== '' && ($seg = self::makeSegment($token, 'wrong', 'Giờ nên được viết bằng số (1-12).'))) {
                $segments[] = $seg;
            }
            $notes[] = 'Giờ nên được viết bằng số từ 1 đến 12.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $hourValid = ValueValidator::validateTime($hour);
        if ($seg = self::makeSegment($hour, $hourValid ? 'correct' : 'wrong', $hourValid ? null : 'Giờ nên từ 1 đến 12.')) {
            $segments[] = $seg;
        }

        self::consumeWhitespace($working, $segments, $notes, '');

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
            if ($seg = self::makeSegment($oclock, 'correct')) {
                $segments[] = $seg;
            }
        } else {
            $token = TokenConsumer::consumeNextTokenSegment($working);
            if ($token !== null && $token !== '') {
                if ($seg = self::makeSegment($token, 'wrong', 'Câu nên kết thúc bằng "o\'clock".')) {
                    $segments[] = $seg;
                }
            }
            $notes[] = 'Câu nên kết thúc bằng "o\'clock".';
        }

        self::consumePunctuation($working, $segments, $notes, '');

        while (($space = TokenConsumer::consumeWhitespaceSegment($working)) !== null) {
            if ($seg = self::makeSegment($space, 'neutral')) {
                $segments[] = $seg;
            }
        }

        if (trim($working) !== '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Câu có phần dư sau "o\'clock".')) {
                $segments[] = $seg;
            }
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildWeather(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $working = trim($answer);

        if (self::checkEmpty($answer, $working, $notes)) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        if (!self::consumePrefix($working, 'today is', $segments, $notes, 'Câu nên bắt đầu với "Today is".')) {
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        self::consumeWhitespace($working, $segments, $notes, 'Thêm khoảng trắng sau "is".');

        if ($working === '') {
            $notes[] = 'Hãy mô tả thời tiết (sunny, rainy, ...).';
            return ['highlight_segments' => $segments, 'notes' => $notes];
        }

        $punctuation = '';
        if (preg_match('/[.!?]$/', $working, $match)) {
            $punctuation = $match[0];
            $working = substr($working, 0, -strlen($punctuation));
        }

        $weatherWord = trim($working);
        if ($weatherWord === '') {
            if ($seg = self::makeSegment($working, 'wrong', 'Bạn chưa ghi loại thời tiết.')) {
                $segments[] = $seg;
            }
        } else {
            $validWeather = ValueValidator::validateWeather($weatherWord);
            if ($seg = self::makeSegment($working, $validWeather ? 'correct' : 'wrong', $validWeather ? null : 'Dùng một trong các từ: sunny, rainy, cloudy, windy, snowy.')) {
                $segments[] = $seg;
            }
        }

        if ($punctuation !== '') {
            if ($seg = self::makeSegment($punctuation, 'correct')) {
                $segments[] = $seg;
            }
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }

    private static function buildWordUsage(string $answer, ?string $extractedValue, bool $isValid): array
    {
        $segments = [];
        $notes = [];
        $trimmed = trim($answer);

        if ($trimmed === '') {
            $notes[] = 'Bạn chưa nhập câu trả lời.';
            return ['highlight_segments' => $segments, 'notes' => $notes];
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

        if ($seg = self::makeSegment($answer, ($lengthValid && $punctuationValid && $capitalValid && $isValid) ? 'correct' : 'wrong')) {
            $segments[] = $seg;
        }

        return ['highlight_segments' => $segments, 'notes' => $notes];
    }
}
