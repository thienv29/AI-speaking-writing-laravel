<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Exercise;
use App\Models\Question;
use App\Models\Attempt;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'lessons' => Lesson::count(),
            'exercises' => Exercise::count(),
            'questions' => Question::count(),
            'users' => User::count(),
            'attempts' => Attempt::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
