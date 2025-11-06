<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AI Speaking - Writing Laravel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @if (app()->environment('local'))
        <!-- Khi dev, load từ server Vite -->
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
    @else
        <!-- Khi build, load file đã compile -->
        <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    {{-- Header --}}
    @include('components.header')

    {{-- Nội dung chính --}}
    <main class="flex-1 container mx-auto p-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

</body>
</html>
