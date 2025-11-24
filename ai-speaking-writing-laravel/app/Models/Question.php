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
        'exercise_question_id',
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

    public function exercises()
    {
        return $this->belongsToMany(
            Exercise::class,
            'exercise_question',
            'question_id',
            'exercise_id'
        )->withPivot('order_index')
        ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'exercise_question_id')
                    ->whereIn('exercise_question_id', function ($query) {
                        $query->select('id')
                              ->from('exercise_question')
                              ->where('question_id', $this->id);
                    });
    }
}
