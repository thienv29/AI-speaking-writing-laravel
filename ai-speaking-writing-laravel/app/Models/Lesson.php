<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'img_url', 
        'level', 
        'active'
    ];

    protected $attributes = [
        'description' => null,
        'img_url'     => null,
        'level'=> null,
        'active' => true,
    ];

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'lesson_id');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'lesson_id');
    }
}
