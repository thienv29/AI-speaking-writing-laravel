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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Common CSS -->
    <link rel="stylesheet" href="/css/header.css">
    
    <!-- Page-specific CSS -->
    @stack('styles')
</head>
<body>
    <div class="floating-bubbles">
        <span class="bubble one"></span>
        <span class="bubble two"></span>
        <span class="bubble three"></span>
    </div>

    @hasSection('topbar')
        @yield('topbar')
    @endif

    <header>
        <div class="logo-area">
            <img src="/assets/images/logo.png" alt="I-CLC Logo" class="logo-img">
            <div>
                <strong>I-CLC</strong><br>
            </div>
        </div>
        <nav>
            @yield('navigation')
        </nav>
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

