<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title', 
        'description', 
        'img_url', 
        'level', 
    ];

    protected $attributes = [
        'description' => null,
        'img_url'     => null,
        'level'=> null,
    ];

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'lesson_id');
    }

    public function questions()
    {
        return $this->hasManyThrough(
            Question::class,  
            Exercise::class,  
            'lesson_id',      
            'exercise_id',    
            'id',            
            'id'             
        );
    }
}
