@extends('admin.layout')

@section('title', 'Chỉnh sửa Nhóm câu hỏi')

@section('content')
<div class="px-4 py-6 sm:px-0 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Nhóm câu hỏi</h1>
        <p class="mt-2 text-gray-600">Cập nhật thông tin nhóm</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form action="{{ route('admin.groups.update', $group) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên nhóm *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $group->name) }}"
                    placeholder="Ví dụ: Từ vựng động vật, Ngữ pháp cơ bản..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    required>

                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.groups.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>

    <!-- Add Questions Section -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Thêm câu hỏi vào nhóm</h2>
        <form action="{{ route('admin.groups.add-questions', $group) }}" method="POST" id="addQuestionsForm">
            @csrf
            <div class="mb-4">
                <label for="question_search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm câu hỏi</label>
                <input type="text" id="question_search" placeholder="Nhập ID hoặc nội dung câu hỏi..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    onkeyup="filterQuestions()">
            </div>
            <div class="mb-4 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                @if($availableQuestions->count() > 0)
                    @foreach($availableQuestions as $question)
                        @php
                            $exercise = $question->exercise ?? $question->exercises->first();
                        @endphp
                        <label class="flex items-start p-2 hover:bg-gray-50 rounded cursor-pointer question-option" data-id="{{ $question->id }}" data-text="{{ strtolower($question->prompt_text ?? '') }}">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" 
                                class="mt-1 mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    ID: {{ $question->id }} - {{ Str::limit($question->prompt_text, 60) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $exercise->type->code ?? 'N/A' }} • {{ $exercise->lesson->title ?? 'N/A' }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tất cả câu hỏi đã được thêm vào nhóm này</p>
                @endif
            </div>
            <div class="flex justify-between items-center">
                <button type="button" onclick="selectAllQuestions()" class="text-sm text-blue-600 hover:text-blue-800">
                    Chọn tất cả
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Thêm vào nhóm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterQuestions() {
        const search = document.getElementById('question_search').value.toLowerCase();
        const options = document.querySelectorAll('.question-option');
        
        options.forEach(option => {
            const text = option.getAttribute('data-text');
            const id = option.getAttribute('data-id');
            if (text.includes(search) || id.includes(search)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    }

    function selectAllQuestions() {
        const checkboxes = document.querySelectorAll('#addQuestionsForm input[type="checkbox"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endsection
