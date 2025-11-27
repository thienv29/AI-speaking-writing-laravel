<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'exercise_id',
        'img_url',
        'audio_url',
        'order_index',
        'prompt_text',
        'target_text',
        'starter_text'
    ];

    protected $attributes = [
        'img_url'     => null,
        'audio_url'   => null,
        'prompt_text' => null,
        'target_text' => null,
        'starter_text'=> null
    ];

    /**
     * Many-to-Many relationship with Exercise
     */
    public function exercises()
    {
        return $this->belongsToMany(
            Exercise::class,
            'exercise_question',
            'question_id',
            'exercise_id'
        )->withPivot('order_index')
        ->orderBy('exercise_question.order_index')
        ->withTimestamps();
    }

    /**
     * Primary exercise relationship (backward compatibility)
     * Keeps supporting legacy code that expects question->exercise
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'question_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_question');
    }
}
