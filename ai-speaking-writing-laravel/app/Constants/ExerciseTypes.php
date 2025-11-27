<?php

namespace App\Constants;

/**
 * Exercise Type Constants
 */
class ExerciseTypes
{
    // Writing exercise types
    public const WAQ = 'WAQ'; // Write Answer to Question
    public const WCS = 'WCS'; // Write Complete Sentence
    public const WSG = 'WSG'; // Write Sentence with Given word
    
    // Speaking exercise types
    public const SPW = 'SPW'; // Speaking - Word
    public const SPS = 'SPS'; // Speaking - Sentence
    
    /**
     * Get all writing exercise type codes
     * 
     * @return array
     */
    public static function writingTypes(): array
    {
        return [self::WAQ, self::WCS, self::WSG];
    }
    
    /**
     * Get all speaking exercise type codes
     * 
     * @return array
     */
    public static function speakingTypes(): array
    {
        return [self::SPW, self::SPS];
    }
}

