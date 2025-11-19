<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\ExerciseType;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $query = Exercise::with(['lesson', 'type']);

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        // Filter by type
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exercises = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        
        $lessons = Lesson::orderBy('title')->get();
        $types = ExerciseType::orderBy('name')->get();
        
        return view('admin.exercises.index', compact('exercises', 'lessons', 'types'));
    }

    public function create()
    {
        $lessons = Lesson::orderBy('title')->get();
        $types = ExerciseType::orderBy('name')->get();
        return view('admin.exercises.create', compact('lessons', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type_id' => 'required|exists:exercise_types,id',
            'title' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'difficulty' => 'nullable|integer',
            'img_url' => 'nullable|url|max:500',
        ]);

        // Tự động tính order_index: max order_index trong cùng lesson + 1, hoặc 1 nếu chưa có
        $maxOrder = Exercise::where('lesson_id', $validated['lesson_id'])->max('order_index');
        $validated['order_index'] = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Exercise::create($validated);

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'lesson_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.exercises.index', $queryParams)
            ->with('success', 'Bài tập đã được tạo thành công!');
    }

    public function show(Exercise $exercise)
    {
        $exercise->load(['lesson', 'type', 'questions']);
        return view('admin.exercises.show', compact('exercise'));
    }

    public function edit(Exercise $exercise)
    {
        $lessons = Lesson::orderBy('title')->get();
        $types = ExerciseType::orderBy('name')->get();
        return view('admin.exercises.edit', compact('exercise', 'lessons', 'types'));
    }

    public function update(Request $request, Exercise $exercise)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type_id' => 'required|exists:exercise_types,id',
            'title' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'difficulty' => 'nullable|integer',
            'img_url' => 'nullable|url|max:500',
            'order_index' => 'nullable|integer',
        ]);

        $exercise->update($validated);

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'lesson_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.exercises.index', $queryParams)
            ->with('success', 'Bài tập đã được cập nhật thành công!');
    }

    public function destroy(Request $request, Exercise $exercise)
    {
        $exercise->delete();

        // Giữ lại các filter parameters khi redirect
        $queryParams = $request->only(['search', 'lesson_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams); // Loại bỏ các giá trị rỗng

        return redirect()->route('admin.exercises.index', $queryParams)
            ->with('success', 'Bài tập đã được xóa thành công!');
    }

    public function importExcelView() 
    {
        $lessons = Lesson::orderBy('created_at')->get();
        return view('admin.exercises.import', compact('lessons'));
    }

    public function importExcel(Request $request) {
        return 'test';
    }
}
