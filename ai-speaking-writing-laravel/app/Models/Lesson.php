<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'img_url', 'level', 'active'];

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'lesson_id');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'lesson_id');
    }
}
