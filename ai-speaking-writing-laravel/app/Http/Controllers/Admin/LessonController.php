<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::query();

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $lessons = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        return view('admin.lessons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'level' => 'nullable|string|in:easy,medium,hard',
        ]);

        Lesson::create($validated);

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'level', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.lessons.index', $queryParams)
            ->with('success', 'Bài học đã được tạo thành công!');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load('exercises.type');
        return view('admin.lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        return view('admin.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'level' => 'nullable|string|in:easy,medium,hard',
        ]);

        $lesson->update($validated);

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'level', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.lessons.index', $queryParams)
            ->with('success', 'Bài học đã được cập nhật thành công!');
    }

    public function destroy(Request $request, Lesson $lesson)
    {
        $lesson->delete();

        // Giữ lại các filter parameters khi redirect
        $queryParams = $request->only(['search', 'level', 'page']);
        $queryParams = array_filter($queryParams); // Loại bỏ các giá trị rỗng

        return redirect()->route('admin.lessons.index', $queryParams)
            ->with('success', 'Bài học đã được xóa thành công!');
    }
}
