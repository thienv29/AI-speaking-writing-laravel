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
                            <span class="exercise-icon-emoji">📝</span>
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
