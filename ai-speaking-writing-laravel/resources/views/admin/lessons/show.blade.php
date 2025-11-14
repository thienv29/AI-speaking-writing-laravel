@extends('admin.layout')

@section('title', 'Chi tiết bài học')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $lesson->title }}</h1>
            <p class="mt-2 text-gray-600">Chi tiết bài học</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                Sửa
            </a>
            <a href="{{ route('admin.lessons.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $lesson->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Level</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $lesson->level ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $lesson->description ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">URL hình ảnh</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($lesson->img_url)
                        <a href="{{ $lesson->img_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $lesson->img_url }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <!-- Exercises List -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Danh sách bài tập ({{ $lesson->exercises->count() }})</h2>
            <a href="{{ route('admin.exercises.create') }}?lesson_id={{ $lesson->id }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                + Thêm bài tập
            </a>
        </div>
        
        @if($lesson->exercises->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($lesson->exercises as $exercise)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $exercise->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exercise->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exercise->type->code ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.exercises.show', $exercise) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-gray-500 text-center py-4">Chưa có bài tập nào</p>
        @endif
    </div>
</div>
@endsection
