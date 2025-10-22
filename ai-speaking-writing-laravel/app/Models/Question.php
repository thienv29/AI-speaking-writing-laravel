<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'img_url',
        'audio_url',
        'order_index',
        'prompt_text',
        'target_text',
        'starter_text',
        'active'
    ];

    protected $attributes = [
        'img_url'     => null,
        'audio_url'   => null,
        'prompt_text' => null,
        'target_text' => null,
        'starter_text'=> null,
        'active'      => true,
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'question_id');
    }
}
