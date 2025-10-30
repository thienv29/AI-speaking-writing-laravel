<?php

namespace App\Services;

use App\Services\TranslationService;

/**
 * Spell checker helper for hobby and other vocabulary
 * 
 * NOTE: Đây là code demo - từ điển được hardcode trong config file
 * Khi triển khai thực tế, có thể:
 * 1. Load từ database (table vocabulary hoặc question_keywords)
 * 2. Load từng bài học (lesson-specific vocabulary)
 * 3. Dynamic loading từ API
 */
class SpellChecker
{
    /**
     * Get vocabulary dictionary for a category
     * 
     * @param string $category e.g., 'hobby', 'location', etc.
     * @return array Empty array if category not found
     */
    private static function getDictionary(string $category): array
    {
        $config = config("spellchecker.{$category}");
        
        if (!is_array($config)) {
            return [];
        }
        
        // Merge actions and objects if they exist
        $dictionary = [];
        if (isset($config['actions']) && is_array($config['actions'])) {
            $dictionary = array_merge($dictionary, $config['actions']);
        }
        if (isset($config['objects']) && is_array($config['objects'])) {
            $dictionary = array_merge($dictionary, $config['objects']);
        }
        
        // If config is a simple array, return it directly
        if (empty($dictionary) && is_array($config)) {
            $dictionary = $config;
        }
        
        return $dictionary;
    }
    
    /**
     * Check if word is valid using Translation API as fallback
     * 
     * @param string $word The word to check
     * @return bool True if word can be translated (likely valid), false otherwise
     */
    private static function validateWithTranslationAPI(string $word): bool
    {
        // Skip if word is too short or too long
        if (strlen($word) < 2 || strlen($word) > 50) {
            return false;
        }
        
        // Try to translate - if API can translate it, likely it's a valid English word
        $translation = TranslationService::translate($word);
        
        // If translation succeeds (from API or dictionary) and result is meaningful
        if ($translation['source'] === 'api' || $translation['source'] === 'dictionary' || $translation['source'] === 'cache') {
            $translatedText = $translation['translation'];
            
            // If translation exists and is different from error message
            // Accept if translation is not empty and not "Không tìm thấy bản dịch"
            if (!empty($translatedText) && 
                $translatedText !== 'Không tìm thấy bản dịch' &&
                strlen($translatedText) > 0) {
                // Additional check: if translation is same as original, might be invalid
                // But if different or contains Vietnamese characters, it's valid
                if ($translatedText !== $word || preg_match('/[àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđ]/u', $translatedText)) {
                    return true; // Word is valid (can be translated)
                }
            }
        }
        
        return false; // Word cannot be translated or translation failed
    }
    
    /**
     * Find the closest matching word from a dictionary
     * 
     * @param string $word The word to check
     * @param array $dictionary Array of valid words
     * @param int $maxDistance Maximum Levenshtein distance allowed
     * @return array|null Returns ['word' => suggested_word, 'distance' => distance] or null if no match found
     */
    public static function suggestCorrection(string $word, array $dictionary, int $maxDistance = 2): ?array
    {
        $word = strtolower(trim($word));
        $bestMatch = null;
        $bestDistance = 999;
        
        foreach ($dictionary as $validWord) {
            $distance = levenshtein($word, $validWord);
            
            // Only consider if length is similar (within 2 chars)
            if (abs(strlen($word) - strlen($validWord)) <= 2) {
                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $bestMatch = $validWord;
                }
            }
        }
        
        if ($bestMatch !== null && $bestDistance <= $maxDistance) {
            return [
                'word' => $bestMatch,
                'distance' => $bestDistance,
                'isTypo' => $bestDistance > 0
            ];
        }
        
        return null;
    }
    
    /**
     * Check spelling for hobby words and return suggestions
     * Uses Translation API as fallback if word not found in dictionary
     * 
     * @param string $hobby The hobby phrase to check
     * @return array ['isValid' => bool, 'suggestions' => array, 'errors' => array]
     */
    public static function checkHobbySpelling(string $hobby): array
    {
        // Load dictionary from config file
        // TODO: Khi triển khai thực tế, có thể load từ database:
        // $dictionary = DB::table('vocabulary')->where('category', 'hobby')->pluck('word')->toArray();
        $validHobbyActions = config('spellchecker.hobby.actions', []);
        $validHobbyObjects = config('spellchecker.hobby.objects', []);
        
        $allValidWords = array_merge($validHobbyActions, $validHobbyObjects);
        
        $hobbyLower = strtolower(trim($hobby));
        $words = preg_split('/\s+/', $hobbyLower);
        $cleanWords = array_map(function($word) {
            return preg_replace('/[^a-z]/', '', strtolower($word));
        }, $words);
        
        $errors = [];
        $suggestions = [];
        $isValid = true;
        
        foreach ($cleanWords as $word) {
            if (strlen($word) < 2) continue;
            
            // Check exact match in dictionary
            if (in_array($word, $allValidWords)) {
                continue; // Word is valid, skip
            }
            
            // Find suggestion using Levenshtein distance
            $suggestion = self::suggestCorrection($word, $allValidWords, 2);
            
            if ($suggestion === null) {
                // Word not found in dictionary and no close match
                // Try using Translation API as fallback
                $isValidViaAPI = self::validateWithTranslationAPI($word);
                
                if ($isValidViaAPI) {
                    // Word is valid according to Translation API
                    continue; // Treat as valid
                } else {
                    // Word is invalid - not in dictionary and cannot be translated
                    $isValid = false;
                    $errors[] = [
                        'word' => $word,
                        'suggestion' => null,
                        'message' => "Từ '$word' không có trong từ điển và không phải là từ tiếng Anh hợp lệ. Hãy sử dụng từ vựng về sở thích đã học."
                    ];
                }
            } elseif ($suggestion['distance'] > 0) {
                // Has suggestion (possible typo) - check if word is actually valid via Translation API
                // If Translation API confirms it's valid, treat as valid word (not a typo)
                $isValidViaAPI = self::validateWithTranslationAPI($word);
                
                if ($isValidViaAPI) {
                    // Word is valid according to Translation API, even though it's similar to another word
                    // This handles cases like "flying" vs "playing" - both are valid but different
                    continue; // Treat as valid
                }
                
                // Word is likely a typo - suggest correction
                $errors[] = [
                    'word' => $word,
                    'suggestion' => $suggestion['word'],
                    'distance' => $suggestion['distance'],
                    'message' => "Có thể bạn muốn nói: '{$suggestion['word']}' (bạn đã ghi: '$word')"
                ];
                
                // Only invalid if distance > 1 (major typo)
                if ($suggestion['distance'] > 1) {
                    $isValid = false;
                }
                
                $suggestions[] = $suggestion;
            }
        }
        
        return [
            'isValid' => $isValid,
            'suggestions' => $suggestions,
            'errors' => $errors
        ];
    }
}

