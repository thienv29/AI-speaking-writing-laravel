<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model 
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type_id',
        'lesson_id',
        'title',
        'instruction',
        'difficulty',
        'img_url',
        'order_index'
    ];

    protected $attributes = [
        'img_url'    => null,
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function type()
    {
        return $this->belongsTo(ExerciseType::class, 'type_id');
    }

    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'exercise_question',
            'exercise_id',
            'question_id'
        )->withPivot('order_index')
        ->orderBy('exercise_question.order_index')
        ->withTimestamps();
    }
}
