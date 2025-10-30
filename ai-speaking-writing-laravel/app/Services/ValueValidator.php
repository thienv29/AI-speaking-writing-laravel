<?php

namespace App\Services;

use App\Services\SpellChecker;

/**
 * Validation methods for different data types
 */
class ValueValidator
{
    /**
     * Validate name (2-50 chars, letters and spaces only)
     */
    public static function validateName(string $name): bool
    {
        return strlen($name) >= 2 && 
               strlen($name) <= 50 && 
               preg_match('/^[A-Za-z\s]+$/', $name);
    }

    /**
     * Validate age (1-120)
     */
    public static function validateAge(string $age): bool
    {
        $ageNum = (int) $age;
        return $ageNum >= 1 && $ageNum <= 120;
    }

    /**
     * Validate hobby (3-100 chars)
     * Checks for reasonable hobby vocabulary and allows minor spelling errors
     * Uses SpellChecker for better error detection and suggestions
     */
    public static function validateHobby(string $hobby): bool
    {
        // Length check
        if (strlen($hobby) < 3 || strlen($hobby) > 100) {
            return false;
        }
        
        $spellCheck = SpellChecker::checkHobbySpelling($hobby);
        return $spellCheck['isValid'];
    }
    
    /**
     * Validate hobby and return detailed spelling information
     * 
     * @param string $hobby
     * @return array ['isValid' => bool, 'suggestions' => array, 'errors' => array]
     */
    public static function validateHobbyWithSuggestions(string $hobby): array
    {
        if (strlen($hobby) < 3 || strlen($hobby) > 100) {
            return [
                'isValid' => false,
                'suggestions' => [],
                'errors' => [['word' => $hobby, 'message' => 'Độ dài không hợp lý (3-100 ký tự).']]
            ];
        }
        
        return SpellChecker::checkHobbySpelling($hobby);
    }

    /**
     * Validate location (2-100 chars)
     */
    public static function validateLocation(string $location): bool
    {
        return strlen($location) >= 2 && strlen($location) <= 100;
    }

    /**
     * Validate time (1-12)
     */
    public static function validateTime(string $time): bool
    {
        $hour = (int) $time;
        return $hour >= 1 && $hour <= 12;
    }

    /**
     * Validate weather (sunny, rainy, cloudy, windy, snowy)
     */
    public static function validateWeather(string $weather): bool
    {
        $validWeather = ['sunny', 'rainy', 'cloudy', 'windy', 'snowy'];
        return in_array(strtolower($weather), $validWeather);
    }

    /**
     * Validate word usage sentence (10+ chars, ends with punctuation, starts with capital)
     */
    public static function validateWordUsage(string $sentence): bool
    {
        return strlen($sentence) >= 10 && 
               preg_match('/[.!?]$/', $sentence) &&
               preg_match('/^[A-Z]/', $sentence);
    }
}

