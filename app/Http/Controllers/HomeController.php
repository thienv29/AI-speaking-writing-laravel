<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $lessonsCount = Lesson::count();
        $usersCount = User::count();
        $completedExercises = 0; // TODO: Calculate from attempts table
        
        return view('dashboard', compact('lessonsCount', 'usersCount', 'completedExercises'));
    }
}

