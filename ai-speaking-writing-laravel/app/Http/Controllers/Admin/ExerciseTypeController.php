<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseType;

class ExerciseTypeController extends Controller
{
    public function index()
    {
        $types = ExerciseType::withCount('exercises')->orderBy('name')->get();
        return view('admin.exercise-types.index', compact('types'));
    }
}
