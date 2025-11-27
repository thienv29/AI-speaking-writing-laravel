<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $groups = $query->withCount('questions')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name'
        ]);

        Group::create([
            'name' => $request->name
        ]);

        return redirect()->route('admin.groups.index')->with('success', 'Nhóm câu hỏi đã được tạo thành công!');
    }

    public function show(Group $group)
    {
        $group->load(['questions' => function($query) {
            $query->with(['exercise.lesson', 'exercise.type'])->orderBy('order_index');
        }]);

        // Get all questions for selection (excluding already in group)
        $existingQuestionIds = $group->questions->pluck('id')->toArray();
        $availableQuestions = \App\Models\Question::with(['exercise.lesson', 'exercise.type'])
            ->whereNotIn('id', $existingQuestionIds)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.groups.show', compact('group', 'availableQuestions'));
    }

    public function edit(Group $group)
    {
        // Get all questions for selection (excluding already in group)
        $existingQuestionIds = $group->questions->pluck('id')->toArray();
        $availableQuestions = \App\Models\Question::with(['exercise.lesson', 'exercise.type'])
            ->whereNotIn('id', $existingQuestionIds)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.groups.edit', compact('group', 'availableQuestions'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id
        ]);

        $group->update([
            'name' => $request->name
        ]);

        return redirect()->route('admin.groups.index')->with('success', 'Nhóm câu hỏi đã được cập nhật thành công!');
    }

    public function addQuestions(Request $request, Group $group)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $questionIds = $request->input('question_ids');
        
        // Add questions to group (sync without detaching to keep existing)
        $group->questions()->syncWithoutDetaching($questionIds);

        $count = count($questionIds);
        return redirect()->route('admin.groups.show', $group)
            ->with('success', "Đã thêm {$count} câu hỏi vào nhóm thành công!");
    }

    public function removeQuestion(Group $group, $questionId)
    {
        $group->questions()->detach($questionId);

        return redirect()->route('admin.groups.show', $group)
            ->with('success', 'Đã xóa câu hỏi khỏi nhóm thành công!');
    }

    public function destroy(Group $group)
    {
        if ($group->questions()->count() > 0) {
            return redirect()->route('admin.groups.index')->with('error', 'Không thể xóa nhóm này vì vẫn còn câu hỏi thuộc nhóm!');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')->with('success', 'Nhóm câu hỏi đã được xóa thành công!');
    }
}
