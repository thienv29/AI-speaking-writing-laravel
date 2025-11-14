@extends('admin.layout')

@section('title', 'Quản lý Câu hỏi')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Câu hỏi</h1>
            <p class="mt-2 text-gray-600">Danh sách tất cả câu hỏi trong hệ thống</p>
        </div>
        <a href="{{ route('admin.questions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tạo câu hỏi mới
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.questions.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Tìm theo đề bài..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="min-w-[180px]">
                <label for="lesson_id" class="block text-sm font-medium text-gray-700 mb-2">Bài học</label>
                <select id="lesson_id" name="lesson_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[180px]">
                <label for="exercise_id" class="block text-sm font-medium text-gray-700 mb-2">Bài tập</label>
                <select id="exercise_id" name="exercise_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}" {{ request('exercise_id') == $exercise->id ? 'selected' : '' }}>
                            {{ $exercise->title }} ({{ $exercise->type->code ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Loại</label>
                <select id="type_id" name="type_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lọc
                </button>
                <a href="{{ route('admin.questions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thứ tự</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đề bài</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài tập</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($questions as $question)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $question->order_index ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($question->prompt_text, 50) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $question->exercise->title ?? '—' }}<br>
                        <span class="text-xs text-gray-400">{{ $question->exercise->lesson->title ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.questions.show', $question) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                            <a href="{{ route('admin.questions.edit', $question) . '?' . http_build_query(request()->only(['search', 'lesson_id', 'exercise_id', 'type_id', 'page'])) }}" class="text-yellow-600 hover:text-yellow-900">Sửa</a>
                            <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?');">
                                @csrf
                                @method('DELETE')
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
                                <button type="submit" class="text-red-600 hover:text-red-900 cursor-pointer">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có câu hỏi nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $questions->links() }}
    </div>
</div>
@endsection
