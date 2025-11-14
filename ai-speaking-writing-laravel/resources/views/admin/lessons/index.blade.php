@extends('admin.layout')

@section('title', 'Quản lý Bài học')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Bài học</h1>
            <p class="mt-2 text-gray-600">Danh sách tất cả bài học trong hệ thống</p>
        </div>
        <a href="{{ route('admin.lessons.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tạo bài học mới
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.lessons.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Tìm theo tiêu đề..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="min-w-[150px]">
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                <select id="level" name="level"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="easy" {{ request('level') == 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ request('level') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ request('level') == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lọc
                </button>
                <a href="{{ route('admin.lessons.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lessons as $lesson)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $lesson->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $lesson->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($lesson->description, 50) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lesson->level ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.lessons.show', $lesson) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                            <a href="{{ route('admin.lessons.edit', $lesson) . '?' . http_build_query(request()->only(['search', 'level', 'page'])) }}" class="text-yellow-600 hover:text-yellow-900">Sửa</a>
                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa bài học này?');">
                                @csrf
                                @method('DELETE')
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('level'))
                                    <input type="hidden" name="level" value="{{ request('level') }}">
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
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có bài học nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $lessons->links() }}
    </div>
</div>
@endsection
