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
                <a href="/">Trang chủ</a>
                <a href="/#contact">Liên hệ</a>
                <a class="cta-btn" href="{{ route('user.lessons') }}">
                    Luyện tiếng Anh
                    <span class="btn-kids-decoration">
                        <img src="/assets/images/home-kids-1.png" alt="Kids" class="btn-kids-image">
                    </span>
                </a>
            </nav>
        </div>
</header>

@push('scripts')
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
@endpush