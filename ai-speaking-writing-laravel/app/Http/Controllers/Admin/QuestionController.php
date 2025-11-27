<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exercise;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with([
            'exercises.lesson',
            'exercises.type',
            'exercise.lesson',
            'exercise.type',
            'groups'
        ]);

        // Filter by exercise
        if ($request->filled('exercise_id')) {
            $query->whereHas('exercises', function($q) use ($request) {
                $q->where('exercises.id', $request->exercise_id);
            });
        }

        // Filter by lesson (through exercise)
        if ($request->filled('lesson_id')) {
            $query->whereHas('exercises', function($q) use ($request) {
                $q->where('lesson_id', $request->lesson_id);
            });
        }

        // Filter by type (through exercise)
        if ($request->filled('type_id')) {
            $query->whereHas('exercises', function($q) use ($request) {
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
            'exercise_ids' => 'required|array|min:1',
            'exercise_ids.*' => 'exists:exercises,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'prompt_text' => 'required|string',
            'target_text' => 'nullable|string',
            'starter_text' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
        ]);

        $exerciseIds = $validated['exercise_ids'];
        $primaryExerciseId = $exerciseIds[0];

        // Set legacy exercise_id + order_index for backward compatibility
        $maxOrder = DB::table('exercise_question')->where('exercise_id', $primaryExerciseId)->max('order_index');
        $orderIndex = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        $groupIds = $validated['group_ids'] ?? [];

        $question = Question::create([
            'exercise_id' => $primaryExerciseId,
            'order_index' => $orderIndex,
            'prompt_text' => $validated['prompt_text'],
            'target_text' => $validated['target_text'] ?? null,
            'starter_text' => $validated['starter_text'] ?? null,
            'img_url' => $validated['img_url'] ?? null,
            'audio_url' => $validated['audio_url'] ?? null,
        ]);

        // Sync exercises with pivot order_index
        $pivotData = [];
        foreach ($exerciseIds as $exerciseId) {
            $maxOrderForExercise = DB::table('exercise_question')->where('exercise_id', $exerciseId)->max('order_index');
            $pivotData[$exerciseId] = [
                'order_index' => ($maxOrderForExercise !== null) ? $maxOrderForExercise + 1 : 1,
            ];
        }
        $question->exercises()->sync($pivotData);
        
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
        $question->load(['exercises.lesson', 'exercises.type', 'groups']);
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
            'exercise_ids' => 'required|array|min:1',
            'exercise_ids.*' => 'exists:exercises,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'prompt_text' => 'required|string',
            'target_text' => 'nullable|string',
            'starter_text' => 'nullable|string',
            'img_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
            'order_index' => 'nullable|integer',
        ]);

        $groupIds = $validated['group_ids'] ?? [];
        $exerciseIds = $validated['exercise_ids'];
        $primaryExerciseId = $exerciseIds[0];

        $question->update([
            'exercise_id' => $primaryExerciseId,
            'prompt_text' => $validated['prompt_text'],
            'target_text' => $validated['target_text'] ?? null,
            'starter_text' => $validated['starter_text'] ?? null,
            'img_url' => $validated['img_url'] ?? null,
            'audio_url' => $validated['audio_url'] ?? null,
            'order_index' => $validated['order_index'] ?? $question->order_index,
        ]);

        // Prepare pivot sync payload
        $currentPivotOrders = $question->exercises->pluck('pivot.order_index', 'id');
        $pivotData = [];
        foreach ($exerciseIds as $exerciseId) {
            if ($currentPivotOrders->has($exerciseId)) {
                $pivotData[$exerciseId] = [
                    'order_index' => $currentPivotOrders[$exerciseId],
                ];
            } else {
                $maxOrderForExercise = DB::table('exercise_question')
                    ->where('exercise_id', $exerciseId)
                    ->where('question_id', '<>', $question->id)
                    ->max('order_index');
                $pivotData[$exerciseId] = [
                    'order_index' => ($maxOrderForExercise !== null) ? $maxOrderForExercise + 1 : 1,
                ];
            }
        }
        $question->exercises()->sync($pivotData);

        // Ensure primary exercise pivot order matches updated order_index (if provided)
        if (isset($validated['order_index'])) {
            $question->exercises()->updateExistingPivot($primaryExerciseId, [
                'order_index' => $question->order_index,
            ]);
        }
        
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

        $questions = Question::with(['exercise.lesson', 'exercise.type', 'exercises.lesson', 'exercises.type'])
            ->where(function($q) use ($query) {
                $q->where('id', $query)
                  ->orWhere('target_text', 'like', '%' . $query . '%')
                  ->orWhere('prompt_text', 'like', '%' . $query . '%')
                  ->orWhere('starter_text', 'like', '%' . $query . '%');
            })
            ->limit(20)
            ->get()
            ->map(function($question) {
                $exercise = $question->exercise ?? $question->exercises->first();
                return [
                    'id' => $question->id,
                    'text' => $question->target_text ?? $question->starter_text ?? 'Question ' . $question->id,
                    'type' => $exercise->type->code ?? 'N/A',
                    'lesson' => $exercise->lesson->title ?? 'N/A',
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
