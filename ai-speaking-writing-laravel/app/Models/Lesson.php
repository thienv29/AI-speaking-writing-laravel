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

    // Note: questions() relationship removed because questions now have many-to-many with exercises via pivot table
    // To get questions for a lesson, use: $lesson->exercises->flatMap->questions
    // To count questions: $lesson->exercises->sum(fn($e) => $e->questions->count())
}
