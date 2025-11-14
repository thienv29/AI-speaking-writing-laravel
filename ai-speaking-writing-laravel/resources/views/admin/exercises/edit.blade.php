@extends('admin.layout')

@section('title', 'Sửa bài tập')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Sửa bài tập</h1>
        <p class="mt-2 text-gray-600">Cập nhật thông tin bài tập</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.exercises.update', $exercise) }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Giữ lại filter parameters --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('lesson_id'))
                <input type="hidden" name="lesson_id" value="{{ request('lesson_id') }}">
            @endif
            @if(request('type_id'))
                <input type="hidden" name="type_id" value="{{ request('type_id') }}">
            @endif
            @if(request('page'))
                <input type="hidden" name="page" value="{{ request('page') }}">
            @endif
            
            <div class="mb-4">
                <label for="lesson_id" class="block text-sm font-medium text-gray-700 mb-2">Bài học *</label>
                <select id="lesson_id" name="lesson_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id', $exercise->lesson_id) == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Loại bài tập *</label>
                <select id="type_id" name="type_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('type_id', $exercise->type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $exercise->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="instruction" class="block text-sm font-medium text-gray-700 mb-2">Hướng dẫn</label>
                <textarea id="instruction" name="instruction" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('instruction', $exercise->instruction) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Độ khó</label>
                <input type="number" id="difficulty" name="difficulty" value="{{ old('difficulty', $exercise->difficulty) }}" min="1" max="5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="img_url" class="block text-sm font-medium text-gray-700 mb-2">URL hình ảnh</label>
                <input type="url" id="img_url" name="img_url" value="{{ old('img_url', $exercise->img_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="order_index" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự</label>
                <input type="number" id="order_index" name="order_index" value="{{ old('order_index', $exercise->order_index) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Thứ tự tự động tính khi tạo mới. Có thể sửa để sắp xếp lại thứ tự hiển thị.</p>
            </div>
            
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <strong>Lưu ý:</strong> ID ({{ $exercise->id }}) là tự động và không thể thay đổi. Khi xóa, ID đó sẽ không được tái sử dụng.
                </p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.exercises.index', request()->only(['search', 'lesson_id', 'type_id', 'page'])) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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
