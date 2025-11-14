@extends('admin.layout')

@section('title', 'Sửa bài học')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Sửa bài học</h1>
        <p class="mt-2 text-gray-600">Cập nhật thông tin bài học</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Giữ lại filter parameters --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('level'))
                <input type="hidden" name="level" value="{{ request('level') }}">
            @endif
            @if(request('page'))
                <input type="hidden" name="page" value="{{ request('page') }}">
            @endif
            
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $lesson->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('description', $lesson->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="img_url" class="block text-sm font-medium text-gray-700 mb-2">URL hình ảnh</label>
                <input type="url" id="img_url" name="img_url" value="{{ old('img_url', $lesson->img_url) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://example.com/image.jpg">
            </div>

            <div class="mb-6">
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                <select id="level" name="level"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Chọn level</option>
                    <option value="easy" {{ old('level', $lesson->level) == 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ old('level', $lesson->level) == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ old('level', $lesson->level) == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>
            
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <strong>Lưu ý:</strong> ID ({{ $lesson->id }}) là tự động và không thể thay đổi. Khi xóa, ID đó sẽ không được tái sử dụng.
                </p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.lessons.index', request()->only(['search', 'level', 'page'])) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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
