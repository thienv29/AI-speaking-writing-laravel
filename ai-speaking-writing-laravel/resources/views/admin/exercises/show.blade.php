@extends('admin.layout')

@section('title', 'Chi tiết bài tập')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $exercise->title }}</h1>
            <p class="mt-2 text-gray-600">Chi tiết bài tập</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.exercises.edit', $exercise) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                Sửa
            </a>
            <a href="{{ route('admin.exercises.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exercise->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Bài học</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exercise->lesson->title ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Loại</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exercise->type->name ?? '—' }} ({{ $exercise->type->code ?? '—' }})</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Độ khó</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exercise->difficulty ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Hướng dẫn</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exercise->instruction ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Questions List -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Danh sách câu hỏi ({{ $exercise->questions->count() }})</h2>
            <a href="{{ route('admin.questions.create') }}?exercise_id={{ $exercise->id }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                + Thêm câu hỏi
            </a>
        </div>
        
        @if($exercise->questions->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thứ tự</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đề bài</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($exercise->questions as $question)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->order_index ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($question->prompt_text, 50) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.questions.show', $question) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-gray-500 text-center py-4">Chưa có câu hỏi nào</p>
        @endif
    </div>
</div>
@endsection
