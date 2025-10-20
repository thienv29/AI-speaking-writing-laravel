<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseType extends Model
{
    use HasFactory;

    protected $table = 'exercise_types'; 
    protected $fillable = ['name', 'code', 'active'];

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'type_id');
    }
}
