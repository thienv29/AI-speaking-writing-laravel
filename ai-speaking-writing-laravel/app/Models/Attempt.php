<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attempt extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'exercise_question_id',
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
        return $this->belongsToMany(
            Question::class,
            'exercise_question', 
            'id',                
            'id',                
            'exercise_question_id', 
            'question_id'        
        );
    }

    // Lấy exercise thông qua bảng pivot
    public function exercise()
    {
        return $this->belongsToMany(
            Exercise::class,
            'exercise_question',
            'id',                
            'id',               
            'exercise_question_id', 
            'exercise_id'      
        );
    }
}
