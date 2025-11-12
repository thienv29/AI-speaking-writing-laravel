@extends('layouts.app')

@section('meta')
<title>Luyện Viết Tiếng Anh - I-CLC Learning Playground</title>
@endsection

{{-- @push('styles')
<link rel="stylesheet" href="/css/writing.css">
@endpush --}}

@section('content')
<section class="writing-hero">
    <div class="hero-content">
        <span class="hero-tagline">📝 Sân chơi luyện  tiếng Anh</span>
        <h1 class="hero-title">
            Luyện Tiếng Anh<br>
            <span class="highlight">Cùng I-CLC</span>
        </h1>
        <p class="hero-description">
            Mỗi bài học có nhiều dạng bài tập giúp bạn phát triển kỹ năng viết và đọc tiếng Anh.
        </p>
    </div>
    <div class="hero-illustration">
        <img src="/assets/images/home-kids-2.png" alt="Kids Writing">
    </div>
</section>

<section class="exercises-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Chọn bài học</h2>
        </div>

        <div class="exercises-grid">
            @foreach($lessons as $index => $lesson)
                @if($lesson->exercises_count > 0)
                <div class="exercise-card" data-animate>
                    <div class="card-image-wrapper">
                        <img src="{{ $lesson->img_url ?? '/assets/images/home-kids-' . (($index % 3) + 1) . '.png' }}" alt="{{ $lesson->title }}" class="card-image">
                    </div>
                    <div class="card-icon">
                        📚
                    </div>
                    <div class="card-content">
                        @if(!empty($lesson->level))
                            <div class="difficulty-badge difficulty-{{ strtolower($lesson->level) }}">
                                {{ strtoupper($lesson->level) }}
                            </div>
                        @endif
                        <h3 class="card-title">{{ $lesson->title }}</h3>
                        @if(!empty($lesson->description))
                            <p class="card-description">{{ $lesson->description }}</p>
                        @endif
                        <p class="card-type">{{ $lesson->exercises_count }} BÀI TẬP | {{ $lesson->questions_count }} CÂU HỎI
                        </p>
                        <a href="{{ route('user.lesson', ['id' => $lesson->id]) }}" 
                            class="card-btn">
                            Xem bài tập
                            <span class="btn-arrow">→</span>
                        </a>
                    </div>
                    <div class="card-decoration"></div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
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
