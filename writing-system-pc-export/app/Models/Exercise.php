<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'lesson_id',
        'title',
        'instruction',
        'difficulty',
        'img_url',
        'order_index'
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
        return $this->hasMany(Question::class, 'exercise_id');
    }
}
