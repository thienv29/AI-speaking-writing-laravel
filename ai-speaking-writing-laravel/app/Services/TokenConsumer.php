<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Helper class for consuming tokens from input strings
 */
class TokenConsumer
{
    /**
     * Consume a case-insensitive literal from input
     */
    public static function consumeCaseInsensitiveLiteral(string &$input, string $expected): ?string
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

    /**
     * Consume whitespace segment
     */
    public static function consumeWhitespaceSegment(string &$input): ?string
    {
        if (preg_match('/^\s+/', $input, $match)) {
            $whitespace = $match[0];
            $input = substr($input, strlen($whitespace));
            return $whitespace;
        }

        return null;
    }

    /**
     * Consume number segment
     */
    public static function consumeNumberSegment(string &$input): ?string
    {
        if (preg_match('/^\d+/', $input, $match)) {
            $number = $match[0];
            $input = substr($input, strlen($number));
            return $number;
        }

        return null;
    }

    /**
     * Consume next token segment
     */
    public static function consumeNextTokenSegment(string &$input): ?string
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

    /**
     * Consume punctuation segment
     */
    public static function consumePunctuationSegment(string &$input): ?string
    {
        if (preg_match('/^[.!?,]/', $input, $match)) {
            $punct = $match[0];
            $input = substr($input, strlen($punct));
            return $punct;
        }

        return null;
    }
}

