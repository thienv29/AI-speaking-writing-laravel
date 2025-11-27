@extends('admin.layout')

@section('title', 'Quản lý Kết quả')

@section('content')
<div class="px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Kết quả</h1>
            <p class="mt-2 text-gray-600">Danh sách tất cả kết quả làm bài trong hệ thống</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.attempts.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Tìm theo tên, email, câu trả lời..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Người dùng</label>
                    <select id="user_id" name="user_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
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
                <div>
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
                <div>
                    <label for="question_id" class="block text-sm font-medium text-gray-700 mb-2">Câu hỏi</label>
                    <select id="question_id" name="question_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        @foreach($questions as $question)
                            <option value="{{ $question->id }}" {{ request('question_id') == $question->id ? 'selected' : '' }}>
                                #{{ $question->id }}: {{ Str::limit($question->prompt_text, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Loại</label>
                    <select id="type_id" name="type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->code }} - {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="is_correct" class="block text-sm font-medium text-gray-700 mb-2">Kết quả</label>
                    <select id="is_correct" name="is_correct"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_correct') === '1' ? 'selected' : '' }}>Đúng</option>
                        <option value="0" {{ request('is_correct') === '0' ? 'selected' : '' }}>Sai</option>
                    </select>
                </div>
                <div>
                    <label for="score_min" class="block text-sm font-medium text-gray-700 mb-2">Điểm tối thiểu</label>
                    <input type="number" id="score_min" name="score_min" value="{{ request('score_min') }}" min="0" max="100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="score_max" class="block text-sm font-medium text-gray-700 mb-2">Điểm tối đa</label>
                    <input type="number" id="score_max" name="score_max" value="{{ request('score_max') }}" min="0" max="100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lọc
                </button>
                <a href="{{ route('admin.attempts.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Xóa bộ lọc
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Câu hỏi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Câu trả lời</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày làm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attempts as $attempt)
                @php
                    $exercise = optional($attempt->question)->exercise ?? optional($attempt->question)->exercises->first();
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attempt->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div>
                            <div class="font-medium">{{ $attempt->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $attempt->user->email ?? 'N/A' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div>
                            <div class="font-medium">#{{ $attempt->question->id ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($attempt->question->prompt_text ?? 'N/A', 40) }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $exercise->title ?? 'N/A' }} 
                                ({{ $exercise->type->code ?? 'N/A' }})
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="max-w-xs">
                            @if($attempt->user_audio_url)
                                <span class="text-blue-600">🎤 Có audio</span><br>
                            @endif
                            {{ Str::limit($attempt->user_answer ?? 'N/A', 50) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($attempt->score !== null)
                            <span class="font-medium">{{ $attempt->score }}/100</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($attempt->is_correct === true)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Đúng</span>
                        @elseif($attempt->is_correct === false)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Sai</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $attempt->created_at ? $attempt->created_at->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.attempts.show', $attempt) }}" class="text-blue-600 hover:text-blue-900">Xem</a>
                            <form action="{{ route('admin.attempts.destroy', $attempt) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa kết quả này?');">
                                @csrf
                                @method('DELETE')
                                @foreach(request()->only(['search', 'user_id', 'question_id', 'exercise_id', 'lesson_id', 'type_id', 'is_correct', 'score_min', 'score_max', 'date_from', 'date_to', 'page']) as $key => $value)
                                    @if($value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <button type="submit" class="text-red-600 hover:text-red-900 cursor-pointer">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có kết quả nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $attempts->links() }}
    </div>
</div>
@endsection

