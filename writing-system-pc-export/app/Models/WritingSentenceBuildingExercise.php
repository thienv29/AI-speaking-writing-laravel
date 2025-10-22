<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingSentenceBuildingExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'title',
        'target_word',
        'word_type',
        'word_meaning',
        'word_example',
        'instructions',
        'sample_sentences',
        'expected_sentence_count',
        'expected_word_count',
        'difficulty',
        'order_index'
    ];

    protected $casts = [
        'sample_sentences' => 'array',
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
        return 'writing_sentence_building_exercise';
    }
}