<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Translation service for English-Vietnamese dictionary
 * 
 * Sử dụng MyMemory Translation API (free tier, up to 10,000 words/day)
 * Fallback to local dictionary nếu API fails hoặc quota exceeded
 */
class TranslationService
{
    /**
     * Translate English word/phrase to Vietnamese
     * 
     * @param string $text Text to translate
     * @return array ['translation' => string, 'source' => string]
     */
    public static function translate(string $text): array
    {
        $text = trim($text);
        
        if (empty($text)) {
            return ['translation' => '', 'source' => 'empty'];
        }
        
        // Cache key
        $cacheKey = 'translation_' . md5(strtolower($text));
        
        // Check cache first (1 day)
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return ['translation' => $cached, 'source' => 'cache'];
        }
        
        // Try to translate using MyMemory Translation API (free, no key required)
        // Free tier: up to 10,000 words/day
        // Cache giúp giảm số lần gọi API xuống đáng kể
        $translation = self::translateWithMyMemory($text);
        $source = 'none';
        
        if ($translation === null) {
            // Fallback: use dictionary lookup if API fails or quota exceeded
            $translation = self::lookupDictionary($text);
            $source = 'dictionary';
        } else {
            $source = 'api';
        }
        
        // Cache the result for 30 days (lâu hơn để giảm API calls)
        // Mỗi từ chỉ gọi API 1 lần trong 30 ngày
        if ($translation !== null && $translation !== '') {
            Cache::put($cacheKey, $translation, now()->addDays(30));
        }
        
        return [
            'translation' => $translation ?? 'Không tìm thấy bản dịch',
            'source' => $source
        ];
    }
    
    /**
     * Translate using MyMemory Translation API (free, no key required for small requests)
     * Free tier: up to 10,000 words/day
     * 
     * NOTE: Cache được dùng để giảm số lần gọi API:
     * - Mỗi từ chỉ gọi API 1 lần trong 30 ngày
     * - Nếu có 100 học sinh, mỗi người dịch 10 từ = 1000 từ
     * - Nhưng nhờ cache, chỉ cần gọi API ~50-100 từ (tùy từ vựng duy nhất)
     * - Thực tế chỉ dùng ~1-2% quota mỗi ngày
     */
    private static function translateWithMyMemory(string $text): ?string
    {
        try {
            $response = Http::timeout(5)->get('https://api.mymemory.translated.net/get', [
                'q' => $text,
                'langpair' => 'en|vi'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['responseData']['translatedText'])) {
                    $translated = $data['responseData']['translatedText'];
                    // MyMemory sometimes returns original text if translation fails
                    if ($translated !== $text && $translated !== '') {
                        return $translated;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Log error but don't fail
            Log::debug('MyMemory API error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Lookup in local dictionary (fallback)
     * Có thể load từ database hoặc config file
     */
    private static function lookupDictionary(string $text): ?string
    {
        $textLower = strtolower(trim($text));
        
        // Simple dictionary lookup - có thể mở rộng từ database
        $dictionary = config('spellchecker.translations', []);
        
        // Check exact match
        if (isset($dictionary[$textLower])) {
            return $dictionary[$textLower];
        }
        
        // Check word variations (remove punctuation, etc.)
        $cleanText = preg_replace('/[^a-z\s]/', '', $textLower);
        if (isset($dictionary[$cleanText])) {
            return $dictionary[$cleanText];
        }
        
        // Check individual words for phrases
        $words = explode(' ', $cleanText);
        if (count($words) > 1) {
            $translations = [];
            foreach ($words as $word) {
                if (strlen($word) > 2 && isset($dictionary[$word])) {
                    $translations[] = $dictionary[$word];
                }
            }
            if (!empty($translations)) {
                return implode(' ', $translations);
            }
        }
        
        // Check single word
        if (count($words) === 1 && isset($dictionary[$words[0]])) {
            return $dictionary[$words[0]];
        }
        
        return null;
    }
    
    /**
     * Batch translate multiple words
     */
    public static function translateBatch(array $texts): array
    {
        $results = [];
        foreach ($texts as $text) {
            $results[$text] = self::translate($text);
        }
        return $results;
    }
}

