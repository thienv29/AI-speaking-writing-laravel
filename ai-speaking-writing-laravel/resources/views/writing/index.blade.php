@extends('layouts.app')

@section('meta')
<title>Luyện Viết Tiếng Anh - I-CLC Learning Playground</title>
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
        <div class="course-header">
            <div class="course-header-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z" fill="#6B2C90"/>
                    <path d="M7 7H17V9H7V7ZM7 11H17V13H7V11ZM7 15H13V17H7V15Z" fill="#6B2C90"/>
                </svg>
            </div>
            <h1 class="course-content-title">NỘI DUNG KHÓA HỌC</h1>
            <p class="course-header-subtitle">Chọn bài học để bắt đầu luyện tập</p>
        </div>
        
        <div class="course-sections">
            @forelse($lessons as $index => $lesson)
            <div class="course-section">
                <div class="section-header" data-section-index="{{ $index }}">
                    <div class="section-title-wrapper">
                        @if(!empty($lesson['level']))
                            <span class="section-level-badge level-{{ strtolower($lesson['level']) }}">
                                {{ strtoupper($lesson['level']) }}
                            </span>
                        @endif
                        <div class="section-title-group">
                            <h2 class="section-title">{{ str_replace('Bài ', 'Unit ', $lesson['title']) }}</h2>
                            @if(!empty($lesson['description']))
                                <span class="section-description">{{ $lesson['description'] }}</span>
                            @endif
                        </div>
                        <span class="section-summary">
                            <svg class="summary-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20Z" fill="currentColor"/>
                            </svg>
                            {{ $lesson['exercises_count'] }} bài tập
                        </span>
                    </div>
                    <div class="section-toggle">
                        <svg class="toggle-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                
                <div class="section-content">
                    @foreach($lesson['exercises'] as $exercise)
                    <div class="exercise-item exercise-type-{{ strtolower($exercise['code']) }}">
                        <div class="exercise-icon">
                            @if($exercise['code'] === 'WAQ')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="#6B2C90" opacity="0.1"/>
                                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#6B2C90" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @elseif($exercise['code'] === 'WCS')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="#2ECC71" opacity="0.1"/>
                                    <path d="M9 12H15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#2ECC71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @elseif($exercise['code'] === 'WSG')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="#3498DB" opacity="0.1"/>
                                    <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20ZM8 12H16M8 16H12" stroke="#3498DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="#6c757d" opacity="0.1"/>
                                    <path d="M10 8L15 12L10 16V8Z" fill="#6c757d"/>
                                </svg>
                            @endif
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
                            <span class="exercise-count">
                                <svg class="count-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.1"/>
                                    <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{ $exercise['questions_count'] }} câu
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="no-exercises">
                <div class="no-exercises-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z" fill="#6B2C90" opacity="0.3"/>
                        <path d="M7 7H17V9H7V7ZM7 11H17V13H7V11ZM7 15H13V17H7V15Z" fill="#6B2C90" opacity="0.5"/>
                    </svg>
                </div>
                <h3>Chưa có bài học</h3>
                <p>Hiện tại chưa có bài học nào. Vui lòng quay lại sau!</p>
                <a href="/" class="back-home-btn">Về trang chủ</a>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@section('footer')
<footer class="writing-footer">
    <div class="footer-content">
        <p>&copy; 2024 I-CLC - Inter-Continental Language Center. All rights reserved.</p>
        <a href="/">← Về trang chủ</a>
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
    
    // Expand first section by default
    const firstSection = document.querySelector('.course-section');
    if (firstSection) {
        firstSection.classList.add('expanded');
        const firstContent = firstSection.querySelector('.section-content');
        if (firstContent) {
            firstContent.style.maxHeight = firstContent.scrollHeight + 'px';
        }
        const firstIcon = firstSection.querySelector('.toggle-icon');
        if (firstIcon) {
            firstIcon.style.transform = 'rotate(180deg)';
        }
    }
</script>
@endpush
