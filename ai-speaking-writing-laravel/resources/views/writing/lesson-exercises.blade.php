@extends('layouts.app')

@section('meta')
<title>{{ str_replace('Bài ', 'Unit ', $lesson->title) }} - I-CLC Learning Playground</title>
@endsection

@section('navigation')
    <a href="/">Trang chủ</a>
    <a href="/#lessons">Bài học</a>
    <a href="/#contact">Liên hệ</a>
    <a class="cta-btn" href="/writing">
        Luyện viết
        <span class="btn-kids-decoration">
            <img src="/assets/images/home-kids-1.png" alt="Kids" class="btn-kids-image">
        </span>
    </a>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/writing.css">
@endpush

@section('content')
<section class="writing-hero">
    <div class="hero-content">
        <span class="hero-tagline">📝 {{ str_replace('Bài ', 'Unit ', $lesson->title) }}</span>
        <h1 class="hero-title">
            Chọn dạng bài tập<br>
            <span class="highlight">Cùng I-CLC</span>
        </h1>
        @if(!empty($lesson->description))
            <p class="hero-description">{{ $lesson->description }}</p>
        @endif
    </div>
    <div class="hero-illustration">
        <img src="{{ $lesson->img_url ?? '/assets/images/home-kids-2.png' }}" alt="{{ $lesson->title }}">
    </div>
</section>

<section class="exercises-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Các dạng bài tập</h2>
            <p class="section-subtitle">Chọn dạng bài tập bạn muốn luyện tập trong bài học này</p>
        </div>

        <div class="exercises-grid">
            @foreach($exercises as $index => $exercise)
            <div class="exercise-card" data-animate>
                <div class="card-image-wrapper">
                    <img src="/assets/images/home-kids-{{ ($index % 3) + 1 }}.png" alt="Kids Learning" class="card-image">
                </div>
                <div class="card-icon">
                    @if($exercise['code'] === 'WAQ')
                        ✍️
                    @elseif($exercise['code'] === 'WCS')
                        ✏️
                    @else
                        📝
                    @endif
                </div>
                <div class="card-content">
                    <h3 class="card-title">{{ $exercise['title'] }}</h3>
                    <p class="card-type">{{ $exercise['type'] }}</p>
                    @if(!empty($exercise['instruction']))
                        <p class="card-description">{{ $exercise['instruction'] }}</p>
                    @endif
                    @if(!empty($exercise['difficulty']))
                        <p class="card-difficulty">Độ khó: {{ $exercise['difficulty'] }}</p>
                    @endif
                    <a href="/writing/question/{{ $exercise['first_question_id'] }}" class="card-btn">
                        Bắt đầu ngay
                        <span class="btn-arrow">→</span>
                    </a>
                </div>
                <div class="card-decoration"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@section('footer')
<footer class="writing-footer">
    <div class="footer-content">
        <p>&copy; 2024 I-CLC - Inter-Continental Language Center. All rights reserved.</p>
        <a href="/writing">← Quay lại danh sách bài học</a>
    </div>
</footer>
@endsection

@push('scripts')
<script>
    // Animate cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 150);
            }
        });
    }, observerOptions);

    document.querySelectorAll('[data-animate]').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Card hover effects
    document.querySelectorAll('.exercise-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
</script>
@endpush

