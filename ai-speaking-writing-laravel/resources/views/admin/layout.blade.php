<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - ICLC</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Assets -->
    @php
        $manifest = null;
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $json = file_get_contents($manifestPath);
            $manifest = $json ? json_decode($json, true) : null;
        }
    @endphp
    @if (!empty($manifest['resources/css/app.css']['file']))
        <link rel="stylesheet" href="{{ '/build/' . ltrim($manifest['resources/css/app.css']['file'], '/') }}">
    @endif
    @if (!empty($manifest['resources/js/app.js']['file']))
        <script type="module" src="{{ '/build/' . ltrim($manifest['resources/js/app.js']['file'], '/') }}" defer></script>
    @endif
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">ICLC Admin</a>
                    <div class="ml-10 flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Dashboard</a>
                        <a href="{{ route('admin.lessons.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Bài học</a>
                        <a href="{{ route('admin.exercises.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Bài tập</a>
                        <a href="{{ route('admin.questions.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Câu hỏi</a>
                        <a href="{{ route('admin.groups.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Nhóm câu hỏi</a>
                        <a href="{{ route('admin.attempts.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Kết quả</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('user.home') }}" class="px-3 py-2 rounded-md hover:bg-blue-700">Về trang chủ</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
