@extends('admin.layout')

@section('title', 'Tạo bài học mới')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tạo bài học mới</h1>
        <p class="mt-2 text-gray-600">Thêm bài học mới vào hệ thống</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.lessons.store') }}" method="POST">
            @csrf
            
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
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="img_url" class="block text-sm font-medium text-gray-700 mb-2">URL hình ảnh</label>
                <input type="url" id="img_url" name="img_url" value="{{ old('img_url') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://example.com/image.jpg">
            </div>

            <div class="mb-6">
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                <select id="level" name="level"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Chọn level</option>
                    <option value="easy" {{ old('level') == 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ old('level') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ old('level') == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.lessons.index', request()->only(['search', 'level', 'page'])) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Tạo bài học
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
