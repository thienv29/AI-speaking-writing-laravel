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
    
    /**
     * Get all writing exercise type codes
     * 
     * @return array
     */
    public static function writingTypes(): array
    {
        return [self::WAQ, self::WCS, self::WSG];
    }
}

