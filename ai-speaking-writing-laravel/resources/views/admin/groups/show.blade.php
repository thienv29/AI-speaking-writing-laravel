@extends('admin.layout')

@section('title', 'Chi tiết Nhóm: ' . $group->name)

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $group->name }}</h1>
                <p class="mt-2 text-gray-600">ID: {{ $group->id }} • Tạo: {{ $group->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.groups.edit', $group) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Chỉnh sửa nhóm
                </a>
                <a href="{{ route('admin.groups.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    ← Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">Q</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Tổng câu hỏi</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $group->questions->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">T</span>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Loại bài tập</h3>
                    <p class="text-sm text-gray-600">{{ $group->questions->pluck('exercise.type.code')->unique()->join(', ') ?: 'Chưa có' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Danh sách câu hỏi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài tập</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($group->questions as $question)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate">{{ Str::limit($question->prompt_text, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($question->exercise->type->code == 'SPW') bg-blue-100 text-blue-800
                                @elseif($question->exercise->type->code == 'SPS') bg-green-100 text-green-800
                                @elseif($question->exercise->type->code == 'WAQ') bg-yellow-100 text-yellow-800
                                @elseif($question->exercise->type->code == 'WCS') bg-purple-100 text-purple-800
                                @elseif($question->exercise->type->code == 'WSG') bg-pink-100 text-pink-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $question->exercise->type->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($question->exercise->title, 30) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có câu hỏi nào trong nhóm</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

