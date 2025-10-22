<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingCompleteSentenceExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'title',
        'sentence_start',
        'instructions',
        'hint_words',
        'sample_completions',
        'expected_word_count',
        'sentence_type',
        'difficulty',
        'order_index'
    ];

    protected $casts = [
        'hint_words' => 'array',
        'sample_completions' => 'array',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function writingAttempts()
    {
        return $this->morphMany(WritingAttempt::class, 'writingExercise');
    }

    /**
     * Get the polymorphic relationship
     */
    public function getWritingExerciseTypeAttribute()
    {
        return 'writing_complete_sentence_exercise';
    }
}