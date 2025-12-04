@extends('admin.layout')

@section('title', 'Sửa câu hỏi')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Sửa câu hỏi</h1>
        <p class="mt-2 text-gray-600">Cập nhật thông tin câu hỏi</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.questions.update', $question) }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Giữ lại filter parameters --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('lesson_id'))
                <input type="hidden" name="lesson_id" value="{{ request('lesson_id') }}">
            @endif
            @if(request('exercise_id'))
                <input type="hidden" name="exercise_id" value="{{ request('exercise_id') }}">
            @endif
            @if(request('type_id'))
                <input type="hidden" name="type_id" value="{{ request('type_id') }}">
            @endif
            @if(request('page'))
                <input type="hidden" name="page" value="{{ request('page') }}">
            @endif
            
            <div class="mb-4">
                <label for="exercise_ids" class="block text-sm font-medium text-gray-700 mb-2">Bài tập (có thể chọn nhiều) *</label>
                <select id="exercise_ids" name="exercise_ids[]" multiple required size="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @php
                        $selectedExercises = old('exercise_ids', ($question->exercises ?? collect())->pluck('id')->toArray());
                    @endphp
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}" {{ in_array($exercise->id, $selectedExercises) ? 'selected' : '' }}>
                            {{ $exercise->title }} ({{ $exercise->lesson->title ?? 'N/A' }} - {{ $exercise->type->code ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Giữ Ctrl (Cmd trên Mac) để chọn nhiều bài tập. Câu hỏi sẽ xuất hiện trong tất cả bài tập đã chọn.</p>
            </div>

            <div class="mb-4">
                <label for="group_ids" class="block text-sm font-medium text-gray-700 mb-2">Nhóm câu hỏi (có thể chọn nhiều)</label>
                <select id="group_ids" name="group_ids[]" multiple
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    size="5">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ in_array($group->id, old('group_ids', $question->groups->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Giữ Ctrl (Cmd trên Mac) để chọn nhiều nhóm</p>
            </div>

            <div class="mb-4">
                <label for="prompt_text" class="block text-sm font-medium text-gray-700 mb-2">Đề bài *</label>
                <textarea id="prompt_text" name="prompt_text" rows="3" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('prompt_text', $question->prompt_text) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="target_text" class="block text-sm font-medium text-gray-700 mb-2">Đáp án (cho speaking)</label>
                <input type="text" id="target_text" name="target_text" value="{{ old('target_text', $question->target_text) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="VD: My name is">
            </div>

            <div class="mb-4">
                <label for="starter_text" class="block text-sm font-medium text-gray-700 mb-2">Gợi ý đầu câu</label>
                <input type="text" id="starter_text" name="starter_text" value="{{ old('starter_text', $question->starter_text) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="VD: I am...">
            </div>

            <div class="mb-4">
                <label for="img_url" class="block text-sm font-medium text-gray-700 mb-2">URL hình ảnh</label>
                <input type="url" id="img_url" name="img_url" value="{{ old('img_url', $question->img_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="audio_url" class="block text-sm font-medium text-gray-700 mb-2">URL audio (cho speaking)</label>
                <input type="url" id="audio_url" name="audio_url" value="{{ old('audio_url', $question->audio_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="order_index" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự</label>
                <input type="number" id="order_index" name="order_index" value="{{ old('order_index', $question->order_index) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Thứ tự tự động tính khi tạo mới. Có thể sửa để sắp xếp lại thứ tự hiển thị.</p>
            </div>
            
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <strong>Lưu ý:</strong> ID ({{ $question->id }}) là tự động và không thể thay đổi. Khi xóa, ID đó sẽ không được tái sử dụng.
                </p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.questions.index', request()->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page'])) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
