@extends('admin.layout')

@section('title', 'Chi tiết Kết quả')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết Kết quả</h1>
            <p class="mt-2 text-gray-600">Thông tin chi tiết về kết quả làm bài</p>
        </div>
        <a href="{{ route('admin.attempts.index') . '?' . http_build_query(request()->only(['search', 'user_id', 'question_id', 'exercise_id', 'lesson_id', 'type_id', 'is_correct', 'score_min', 'score_max', 'date_from', 'date_to', 'page'])) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            ← Quay lại
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Thông tin Kết quả #{{ $attempt->id }}</h2>
        </div>
        <div class="px-6 py-4 space-y-6">
            <!-- User Info -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Người dùng</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900"><strong>ID:</strong> {{ $attempt->user_id }}</p>
                    <p class="text-sm text-gray-900"><strong>Tên:</strong> {{ $attempt->user->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-900"><strong>Email:</strong> {{ $attempt->user->email ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Question Info -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Câu hỏi</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900"><strong>ID:</strong> {{ $attempt->question_id }}</p>
                    <p class="text-sm text-gray-900"><strong>Đề bài:</strong> {{ $attempt->question->prompt_text ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-900"><strong>Đáp án đúng:</strong> {{ $attempt->question->target_text ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <strong>Bài tập:</strong> {{ $attempt->question->exercise->title ?? 'N/A' }} 
                        ({{ $attempt->question->exercise->type->code ?? 'N/A' }})
                    </p>
                    <p class="text-sm text-gray-500">
                        <strong>Bài học:</strong> {{ $attempt->question->exercise->lesson->title ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <!-- User Answer -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Câu trả lời của người dùng</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $attempt->user_answer ?? 'N/A' }}</p>
                    @if($attempt->user_audio_url)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-2">Audio:</p>
                            <audio controls class="w-full max-w-md">
                                <source src="{{ $attempt->user_audio_url }}" type="audio/mpeg">
                                <source src="{{ $attempt->user_audio_url }}" type="audio/wav">
                                <source src="{{ $attempt->user_audio_url }}" type="audio/webm">
                                Trình duyệt không hỗ trợ phát audio.
                            </audio>
                            <a href="{{ $attempt->user_audio_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                Tải xuống audio
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Results -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Kết quả</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Điểm số</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($attempt->score !== null)
                                    {{ $attempt->score }}/100
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kết quả</p>
                            <p class="text-lg font-semibold">
                                @if($attempt->is_correct === true)
                                    <span class="text-green-600">Đúng ✓</span>
                                @elseif($attempt->is_correct === false)
                                    <span class="text-red-600">Sai ✗</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback -->
            @if($attempt->feedback)
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Phản hồi</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $attempt->feedback }}</p>
                </div>
            </div>
            @endif

            <!-- Evaluation Meta -->
            @if($attempt->evaluation_meta)
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Thông tin đánh giá</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-xs text-gray-700 overflow-x-auto">{{ json_encode($attempt->evaluation_meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            <!-- Timestamp -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Thời gian</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900">
                        <strong>Ngày làm:</strong> {{ $attempt->created_at ? $attempt->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <form action="{{ route('admin.attempts.destroy', $attempt) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa kết quả này?');">
                @csrf
                @method('DELETE')
                @foreach(request()->only(['search', 'user_id', 'question_id', 'exercise_id', 'lesson_id', 'type_id', 'is_correct', 'score_min', 'score_max', 'date_from', 'date_to', 'page']) as $key => $value)
                    @if($value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Xóa kết quả
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

