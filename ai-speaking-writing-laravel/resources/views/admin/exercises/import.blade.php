@extends('admin.layout')

@section('title', 'Import Excel bài tập')

@section('content')
<div id="admin-exercises-page-content" class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Import Excel Bài tập</h1>

    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        <form action="/import-excel" method="POST" enctype="multipart/form-data">
            @csrf
            
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
                <div class="mb-4">
                    <input id="excel-file" type="file" name="file" accept=".xlsx" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    @error('file')
                        <p class="text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button id="convert-excel-btn"  type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Tải file Excel
                </button>
            </div>

            <div id="result-box" class="mt-6 p-4 border rounded bg-gray-50 hidden"></div>

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

@push('scripts')
<script>

</script>
@endpush
