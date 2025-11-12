<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Meta Tags -->
    @hasSection('meta')
        @yield('meta')
    @else
        <title>I-CLC Learning Playground</title>
    @endif
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    
    <!-- Fonts - Inter for better Vietnamese support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    @if (app()->environment('local'))
        <!-- Khi dev, load từ server Vite -->
        <link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
    @else
        <!-- Khi build, load file đã compile -->
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
        <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
    @endif
    
    <!-- Page-specific CSS -->
    {{-- @stack('styles') --}}
</head>
<body>
    @include('components.user.topbar')

    @include('components.user.header')

    <main>
        @yield('content')
        @include('components.user.scroll-to-top')
    </main>

    @include('components.user.footer')

    @stack('scripts')
</body>
</html>

