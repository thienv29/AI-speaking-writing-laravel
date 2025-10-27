<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'exercise_types'; 
    protected $fillable = [
        'name',
        'code', 
    ];

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'type_id');
    }
}
