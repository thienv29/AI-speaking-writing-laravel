@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Tổng quan hệ thống</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Bài học</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['lessons'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Bài tập</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['exercises'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Câu hỏi</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['questions'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Người dùng</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['users'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Lần làm bài</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['attempts'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tỷ lệ đúng</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $correctRate }}%</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $correctAttempts }} đúng / {{ $stats['attempts'] }} tổng
                        </p>
                    </div>
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Điểm trung bình</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $avgScore }}/100</p>
                        <p class="mt-1 text-xs text-gray-500">Chỉ tính bài Writing có điểm</p>
                    </div>
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Hôm nay</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $todayAttempts }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Tuần: {{ $weekAttempts }} | Tháng: {{ $monthAttempts }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attempts by Type -->
    @if($attemptsByType->isNotEmpty())
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Lần làm bài theo loại</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach($attemptsByType as $typeStat)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500">{{ $typeStat['name'] }}</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $typeStat['count'] }}</div>
                <div class="mt-1 text-xs text-gray-500">
                    Đúng: {{ $typeStat['correct'] }} ({{ $typeStat['count'] > 0 ? round(($typeStat['correct'] / $typeStat['count']) * 100, 1) : 0 }}%)
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Attempts -->
    @if($recentAttempts->isNotEmpty())
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Lần làm bài gần đây</h2>
            <a href="{{ route('admin.attempts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Xem tất cả →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Câu hỏi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentAttempts as $attempt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $attempt->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ Str::limit($attempt->question->prompt_text ?? 'N/A', 30) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $attempt->question->exercise->type->code ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($attempt->is_correct)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Đúng</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Sai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($attempt->score !== null)
                                {{ $attempt->score }}/100
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $attempt->created_at ? $attempt->created_at->diffForHumans() : 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Warnings -->
    @if($exercisesWithoutQuestions > 0 || $speakingQuestionsWithoutImg > 0)
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h2 class="text-lg font-bold text-yellow-900 mb-3">⚠️ Cần chú ý</h2>
        <ul class="space-y-2 text-sm text-yellow-800">
            @if($exercisesWithoutQuestions > 0)
            <li>• Có <strong>{{ $exercisesWithoutQuestions }}</strong> bài tập chưa có câu hỏi</li>
            @endif
            @if($speakingQuestionsWithoutImg > 0)
            <li>• Có <strong>{{ $speakingQuestionsWithoutImg }}</strong> câu hỏi Speaking chưa có hình ảnh</li>
            @endif
        </ul>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Thao tác nhanh</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <a href="{{ route('admin.lessons.create') }}" class="block px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                <div class="font-medium text-gray-900">Tạo bài học mới</div>
                <div class="text-sm text-gray-500 mt-1">Thêm bài học vào hệ thống</div>
            </a>
            <a href="{{ route('admin.exercises.create') }}" class="block px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                <div class="font-medium text-gray-900">Tạo bài tập mới</div>
                <div class="text-sm text-gray-500 mt-1">Thêm bài tập cho bài học</div>
            </a>
            <a href="{{ route('admin.questions.create') }}" class="block px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                <div class="font-medium text-gray-900">Tạo câu hỏi mới</div>
                <div class="text-sm text-gray-500 mt-1">Thêm câu hỏi cho bài tập</div>
            </a>
        </div>
    </div>
</div>
@endsection
