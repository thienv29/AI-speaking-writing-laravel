@extends('admin.layout')

@section('title', 'Chi tiết câu hỏi')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết câu hỏi</h1>
            <p class="mt-2 text-gray-600">ID: {{ $question->id }}</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.questions.edit', $question) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                Sửa
            </a>
            <a href="{{ route('admin.questions.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Thứ tự</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->order_index ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Bài tập</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->exercise->title ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Bài học</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->exercise->lesson->title ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Loại</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->exercise->type->code ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Nhóm</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->group->name ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Đề bài</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->prompt_text ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Đáp án (speaking)</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->target_text ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Gợi ý đầu câu</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $question->starter_text ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">URL hình ảnh</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($question->img_url)
                        <a href="{{ $question->img_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $question->img_url }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">URL audio</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($question->audio_url)
                        <a href="{{ $question->audio_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $question->audio_url }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection
