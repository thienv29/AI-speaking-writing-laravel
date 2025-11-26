@extends('admin.layout')

@section('title', 'Chỉnh sửa Nhóm câu hỏi')

@section('content')
<div class="px-4 py-6 sm:px-0 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Nhóm câu hỏi</h1>
        <p class="mt-2 text-gray-600">Cập nhật thông tin nhóm</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.groups.update', $group) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên nhóm *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $group->name) }}"
                    placeholder="Ví dụ: Từ vựng động vật, Ngữ pháp cơ bản..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    required>

                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.groups.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
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
