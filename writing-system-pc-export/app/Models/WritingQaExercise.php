<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingQaExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'title',
        'question',
        'instructions',
        'sample_answers',
        'expected_word_count',
        'difficulty',
        'order_index'
    ];

    protected $casts = [
        'sample_answers' => 'array',
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
        return 'writing_qa_exercise';
    }
}