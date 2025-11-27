<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exercise;
use App\Models\Group;
use Illuminate\Http\Request;
class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['exercise.lesson', 'exercise.type', 'groups']);

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

        // Filter by group (many-to-many)
        if ($request->filled('group_id')) {
            $query->whereHas('groups', function($q) use ($request) {
                $q->where('groups.id', $request->group_id);
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
        $groups = Group::orderBy('name')->get();

        return view('admin.questions.index', compact('questions', 'exercises', 'lessons', 'types', 'groups'));
    }

    public function create()
    {
        $exercises = Exercise::with(['lesson', 'type'])
            ->orderBy('id', 'desc')
            ->get();
        $groups = Group::orderBy('name')->get();
        return view('admin.questions.create', compact('exercises', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'prompt_text' => 'required|string',
            'target_text' => 'nullable|string',
            'starter_text' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
        ]);

        // Tự động tính order_index: max order_index trong cùng exercise + 1, hoặc 1 nếu chưa có
        $maxOrder = Question::where('exercise_id', $validated['exercise_id'])->max('order_index');
        $validated['order_index'] = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        // Remove group_ids from validated (not a column in questions table)
        $groupIds = $validated['group_ids'] ?? [];
        unset($validated['group_ids']);

        $question = Question::create($validated);
        
        // Sync groups (many-to-many)
        if (!empty($groupIds)) {
            $question->groups()->sync($groupIds);
        }

        // Giữ lại các filter parameters khi redirect (nếu có từ form)
        $queryParams = $request->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.questions.index', $queryParams)
            ->with('success', 'Câu hỏi đã được tạo thành công!');
    }

    public function show(Question $question)
    {
        $question->load(['exercise.lesson', 'exercise.type', 'groups']);
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $exercises = Exercise::with(['lesson', 'type'])
            ->orderBy('id', 'desc')
            ->get();
        $groups = Group::orderBy('name')->get();
        return view('admin.questions.edit', compact('question', 'exercises', 'groups'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'prompt_text' => 'required|string',
            'target_text' => 'nullable|string',
            'starter_text' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
            'order_index' => 'nullable|integer',
        ]);

        // Remove group_ids from validated (not a column in questions table)
        $groupIds = $validated['group_ids'] ?? [];
        unset($validated['group_ids']);

        $question->update($validated);
        
        // Sync groups (many-to-many)
        $question->groups()->sync($groupIds);

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

    public function bulkAssignGroup(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
        ]);

        $questionIds = $request->input('question_ids');
        $groupIds = $request->input('group_ids', []);

        $questions = Question::whereIn('id', $questionIds)->get();
        
        foreach ($questions as $question) {
            if (empty($groupIds)) {
                // Remove all groups
                $question->groups()->detach();
            } else {
                // Sync groups (add new, keep existing)
                $question->groups()->syncWithoutDetaching($groupIds);
            }
        }

        $count = count($questionIds);
        $groupCount = count($groupIds);
        $message = $groupCount > 0
            ? "Đã gán {$count} câu hỏi vào {$groupCount} nhóm thành công!"
            : "Đã xóa tất cả nhóm khỏi {$count} câu hỏi thành công!";

        // Giữ lại các filter parameters khi redirect
        $queryParams = $request->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.questions.index', $queryParams)
            ->with('success', $message);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $questionIds = $request->input('question_ids');
        $count = Question::whereIn('id', $questionIds)->delete();

        // Giữ lại các filter parameters khi redirect
        $queryParams = $request->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page']);
        $queryParams = array_filter($queryParams);

        return redirect()->route('admin.questions.index', $queryParams)
            ->with('success', "Đã xóa {$count} câu hỏi thành công!");
    }
}
