<?php

namespace App\Models;

use App\Support\Effects;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'exercise_id',
        'img_url',
        'audio_url',
        'order_index',
        'prompt_text',
        'target_text',
        'starter_text'
    ];

    protected $attributes = [
        'img_url'     => null,
        'audio_url'   => null,
        'prompt_text' => null,
        'target_text' => null,
        'starter_text'=> null
    ];

    protected $appends = ['effect'];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'question_id');
    }

    public function getEffectAttribute(): array
    {
        return Effects::forQuestion($this);
    }
}
