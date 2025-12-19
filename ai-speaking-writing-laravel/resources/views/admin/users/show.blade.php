@extends('admin.layout')

@section('title', 'Chi tiết Người dùng')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết Người dùng</h1>
            <p class="mt-2 text-gray-600">Thông tin chi tiết và lịch sử làm bài của người dùng</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                Sửa
            </a>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- User Info Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Thông tin cá nhân</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tên</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Vai trò</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $user->role ?? 'user' }}
                        </span>
                    </dd>
                </div>
                @if($user->dob)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ngày sinh</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($user->dob)->format('d/m/Y') }}</dd>
                </div>
                @endif
                @if($user->phone_number)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->phone_number }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Statistics Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Thống kê</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tổng lần làm bài</dt>
                    <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $user->attempts_count ?? 0 }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Recent Attempts -->
    @if($user->attempts && $user->attempts->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Lần làm bài gần đây</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài học</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài tập</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($user->attempts as $attempt)
                    @php
                        $exercise = $attempt->question->exercises->first();
                        $lesson = $exercise->lesson ?? null;
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $lesson->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $exercise->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $attempt->score ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($attempt->is_correct)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Đúng</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Sai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

