<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Exercise;
use App\Models\ExerciseType;
use Illuminate\Http\Request;

class WritingController extends Controller
{
    public function index()
    {
        // Get Writing exercise types
        $writingTypes = ExerciseType::whereIn('code', ['WAQ', 'WCS', 'WSG'])->get();
        
        // Get sample questions for each type
        $exercises = collect();
        foreach ($writingTypes as $type) {
            $exercise = Exercise::where('type_id', $type->id)->first();
            if ($exercise) {
                $exercises->push([
                    'id' => $exercise->id,
                    'title' => $exercise->title,
                    'type' => $type->name,
                    'code' => $type->code,
                    'instruction' => $exercise->instruction
                ]);
            }
        }
        
        return view('writing.index', compact('exercises'));
    }
    
    public function show($id)
    {
        // Get question by exercise ID
        $question = Question::where('exercise_id', $id)->first();
        
        if (!$question) {
            abort(404, 'Question not found');
        }
        
        $exercise = $question->exercise;
        $exerciseType = $exercise->type;
        
        return view('writing.question', compact('question', 'exercise', 'exerciseType'));
    }
}
