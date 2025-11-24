<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'group_question', 
            'group_id',       
            'question_id'     
        )->withTimestamps();
    }
}

