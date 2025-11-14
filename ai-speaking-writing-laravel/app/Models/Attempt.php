<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'question_id',
        'user_answer',
        'user_audio_url',
        'is_correct',
        'feedback',
        'score',
        'evaluation_meta',
        'created_at'
    ];

    protected $attributes = [
        'user_answer'    => null,
        'user_audio_url' => null,
        'is_correct'     => false,
        'feedback'      => null,
        'score'          => null,
        'evaluation_meta' => null,
    ];
    
    protected $casts = [
        'evaluation_meta' => 'array',
        'is_correct' => 'boolean',
        'score' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
