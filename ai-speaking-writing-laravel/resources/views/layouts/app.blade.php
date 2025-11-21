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
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    
    <!-- Fonts - Inter for better Vietnamese support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
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
        <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
    @endif
    @if (!empty($manifest['resources/js/app.js']['file']))
        <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}" defer></script>
    @endif
</head>
<body>
    @hasSection('topbar')
        @yield('topbar')
    @endif

    <header>
        <div class="header-wrapper">
            <div class="logo-area">
                <a href="/" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit;">
                    <img src="/assets/images/logo.png" alt="I-CLC Logo" class="logo-img">
                    <div>
                        <strong>I-CLC</strong><br>
                    </div>
                </a>
            </div>
            <nav>
                @yield('navigation')
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @hasSection('footer')
        @yield('footer')
    @endif

    @hasSection('scrollToTop')
        @yield('scrollToTop')
    @endif

    <!-- Common JS -->
    <script>
        // Sticky header
        const header = document.querySelector('header');
        if (header) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    header.classList.add('sticky');
                } else {
                    header.classList.remove('sticky');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>

