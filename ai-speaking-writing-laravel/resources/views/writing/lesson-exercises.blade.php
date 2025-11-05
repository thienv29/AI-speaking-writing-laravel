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
<link rel="stylesheet" href="/css/course-content.css">
@endpush

@section('content')
<section class="course-content-section">
    <div class="container">
        <h1 class="course-content-title">NỘI DUNG KHÓA HỌC</h1>
        
        <div class="course-sections">
            <div class="course-section expanded">
                <div class="section-header">
                    <div class="section-title-wrapper">
                        <h2 class="section-title">{{ str_replace('Bài ', 'Unit ', $lesson->title) }}</h2>
                        <span class="section-summary">
                            {{ count($exercises) }} bài tập
                        </span>
                    </div>
                    <div class="section-toggle">
                        <svg class="toggle-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" style="transform: rotate(180deg);">
                            <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                
                <div class="section-content" style="max-height: none;">
                    @foreach($exercises as $exercise)
                    <div class="exercise-item">
                        <div class="exercise-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" fill="#3498DB" opacity="0.1"/>
                                <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20ZM8 12H16M8 16H12" stroke="#3498DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="exercise-info">
                            <a href="/writing/question/{{ $exercise['first_question_id'] }}" class="exercise-title">
                                {{ $exercise['title'] }}
                            </a>
                            @if(!empty($exercise['instruction']))
                                <span class="exercise-subtitle">{{ $exercise['instruction'] }}</span>
                            @endif
                        </div>
                        <div class="exercise-meta">
                            <span class="exercise-count">{{ $exercise['questions_count'] }} câu</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 32px;">
            <a href="/writing" class="back-home-btn" style="background: #6c757d;">
                ← Quay lại danh sách bài học
            </a>
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
    // Collapsible sections
    document.querySelectorAll('.section-header').forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.course-section');
            const content = section.querySelector('.section-content');
            const icon = this.querySelector('.toggle-icon');
            
            section.classList.toggle('expanded');
            
            if (section.classList.contains('expanded')) {
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.style.maxHeight = '0';
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });
</script>
@endpush
