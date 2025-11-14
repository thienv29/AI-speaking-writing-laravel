@extends('admin.layout')

@section('title', 'Tạo bài tập mới')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tạo bài tập mới</h1>
        <p class="mt-2 text-gray-600">Thêm bài tập mới vào hệ thống</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.exercises.store') }}" method="POST">
            @csrf
            
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
                    <option value="">Chọn bài học</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id', request('lesson_id')) == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Loại bài tập *</label>
                <select id="type_id" name="type_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Chọn loại</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="instruction" class="block text-sm font-medium text-gray-700 mb-2">Hướng dẫn</label>
                <textarea id="instruction" name="instruction" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('instruction') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Độ khó</label>
                <input type="number" id="difficulty" name="difficulty" value="{{ old('difficulty') }}" min="1" max="5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="img_url" class="block text-sm font-medium text-gray-700 mb-2">URL hình ảnh</label>
                <input type="url" id="img_url" name="img_url" value="{{ old('img_url') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.exercises.index', request()->only(['search', 'lesson_id', 'type_id', 'page'])) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Tạo bài tập
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
