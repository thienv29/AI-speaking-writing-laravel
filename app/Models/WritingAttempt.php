<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'writing_exercise_type',
        'writing_exercise_id',
        'attempt_number',
        'user_answer',
        'word_count',
        'character_count',
        'time_spent',
        'is_submitted',
        'is_correct',
        'feedback',
        'score',
        'started_at',
        'submitted_at'
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specific writing exercise model
     */
    public function writingExercise()
    {
        switch ($this->writing_exercise_type) {
            case 'writing_qa_exercise':
                return WritingQaExercise::find($this->writing_exercise_id);
            case 'writing_sentence_building_exercise':
                return WritingSentenceBuildingExercise::find($this->writing_exercise_id);
            case 'writing_complete_sentence_exercise':
                return WritingCompleteSentenceExercise::find($this->writing_exercise_id);
            default:
                return null;
        }
    }

    /**
     * Get the specific writing exercise model
     */
    public function getWritingExercise()
    {
        switch ($this->writing_exercise_type) {
            case 'writing_qa_exercise':
                return WritingQaExercise::find($this->writing_exercise_id);
            case 'writing_sentence_building_exercise':
                return WritingSentenceBuildingExercise::find($this->writing_exercise_id);
            case 'writing_complete_sentence_exercise':
                return WritingCompleteSentenceExercise::find($this->writing_exercise_id);
            default:
                return null;
        }
    }

    /**
     * Calculate word count from user answer
     */
    public function calculateWordCount()
    {
        if (empty($this->user_answer)) {
            return 0;
        }
        
        $words = str_word_count(trim($this->user_answer));
        $this->update(['word_count' => $words]);
        return $words;
    }

    /**
     * Calculate character count from user answer
     */
    public function calculateCharacterCount()
    {
        if (empty($this->user_answer)) {
            return 0;
        }
        
        $characters = strlen(trim($this->user_answer));
        $this->update(['character_count' => $characters]);
        return $characters;
    }

    /**
     * Submit the writing attempt
     */
    public function submit()
    {
        $this->update([
            'is_submitted' => true,
            'submitted_at' => now(),
            'word_count' => $this->calculateWordCount(),
            'character_count' => $this->calculateCharacterCount()
        ]);
    }
}