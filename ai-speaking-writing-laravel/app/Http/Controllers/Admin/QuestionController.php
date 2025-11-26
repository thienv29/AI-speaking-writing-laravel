<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exercise;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['exercise.lesson', 'exercise.type']);

        // Filter by exercise
        if ($request->filled('exercise_id')) {
            $query->where('exercise_id', $request->exercise_id);
        }

        // Filter by lesson (through exercise)
        if ($request->filled('lesson_id')) {
            $query->whereHas('exercise', function($q) use ($request) {
                $q->where('lesson_id', $request->lesson_id);
            });
        }

        // Filter by type (through exercise)
        if ($request->filled('type_id')) {
            $query->whereHas('exercise', function($q) use ($request) {
                $q->where('type_id', $request->type_id);
            });
        }

        // Search by prompt_text
        if ($request->filled('search')) {
            $query->where('prompt_text', 'like', '%' . $request->search . '%');
        }

        $questions = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        
        $exercises = Exercise::with(['lesson', 'type'])->orderBy('id', 'desc')->get();
        $lessons = \App\Models\Lesson::orderBy('title')->get();
        $types = \App\Models\ExerciseType::orderBy('name')->get();
        
        return view('admin.questions.index', compact('questions', 'exercises', 'lessons', 'types'));
    }

    public function create()
    {
        $exercises = Exercise::with(['lesson', 'type'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.questions.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'prompt_text' => 'required|string',
            'target_text' => 'nullable|string',
            'starter_text' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
        ]);

        // Tự động tính order_index: max order_index trong cùng exercise + 1, hoặc 1 nếu chưa có
        $maxOrder = Question::where('exercise_id', $validated['exercise_id'])->max('order_index');
        $validated['order_index'] = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        Question::create($validated);

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.questions.index', $queryParams)
            ->with('success', 'Câu hỏi đã được tạo thành công!');
    }

    public function show(Question $question)
    {
        $question->load(['exercise.lesson', 'exercise.type']);
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $exercises = Exercise::with(['lesson', 'type'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.questions.edit', compact('question', 'exercises'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'prompt_text' => 'required|string',
            'target_text' => 'nullable|string',
            'starter_text' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
            'order_index' => 'nullable|integer',
        ]);

        $question->update($validated);

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.questions.index', $queryParams)
            ->with('success', 'Câu hỏi đã được cập nhật thành công!');
    }

    public function destroy(Request $request, Question $question)
    {
        $question->delete();

        // Giữ lại các filter parameters khi redirect
        $queryParams = $request->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams); // Loại bỏ các giá trị rỗng

        return redirect()->route('admin.questions.index', $queryParams)
            ->with('success', 'Câu hỏi đã được xóa thành công!');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $questions = Question::with(['exercise.lesson', 'exercise.type'])
            ->where(function($q) use ($query) {
                $q->where('id', $query)
                  ->orWhere('target_text', 'like', '%' . $query . '%')
                  ->orWhere('prompt_text', 'like', '%' . $query . '%')
                  ->orWhere('starter_text', 'like', '%' . $query . '%');
            })
            ->limit(20)
            ->get()
            ->map(function($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->target_text ?? $question->starter_text ?? 'Question ' . $question->id,
                    'type' => $question->exercise->type->code,
                    'lesson' => $question->exercise->lesson->title,
                ];
            });

        return response()->json($questions);
    }
}
