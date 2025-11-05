@extends('layouts.app')

@section('meta')
<title>Writing Adventure | I-CLC</title>
@endsection

@section('navigation')
    <a href="/">Trang chủ</a>
    <a href="/#contact">Liên hệ</a>
    <a class="cta-btn" href="/writing">
        Luyện viết
        <span class="btn-kids-decoration">
            <img src="/assets/images/home-kids-1.png" alt="Kids" class="btn-kids-image">
        </span>
    </a>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/writing-question.css">
@endpush

@section('content')
<div class="question-page-wrapper">
    <div class="bg-shape one"></div>
    <div class="bg-shape two"></div>

    <div class="container">
        <div class="back-link">
            <a href="/" class="back-button">🏠 Trang chủ</a>
            <a href="/writing" class="back-button">← Quay lại danh sách bài học</a>
        </div>

        <div class="header-card">
            <div class="header-illustration">✍️</div>
            <div class="header-content">
                <span class="tagline" id="lessonBadge">Sân chơi viết tiếng Anh</span>
                <h1>Writing Adventure <span id="exerciseTitleHeading">...</span></h1>
                <p class="header-sub" id="exerciseSubtitle">Mỗi câu trả lời đúng sẽ mang đến sticker và pháo bông dành riêng cho bé.</p>
                <div class="tag-list">
                    <span class="tag">Template: <strong id="templateTag">—</strong></span>
                    <span class="tag">Loại bài: <strong id="typeTag">—</strong></span>
                    <span class="tag">Câu số <strong id="orderTag">#1</strong></span>
                </div>
                @if(isset($allQuestions) && $allQuestions->count() > 1)
                <div class="question-navigation">
                    <div class="question-nav-controls">
                        <button id="prevQuestionBtn" class="nav-btn" onclick="navigateToPrevious()" title="Câu trước">
                            ← Trước
                        </button>
                        <div class="question-selector">
                            <label for="questionSelect" class="question-selector-label">Câu hỏi:</label>
                            <select id="questionSelect" class="question-select" onchange="navigateToQuestion(this.value)">
                                @foreach($allQuestions as $q)
                                    <option value="{{ $q->id }}" {{ $q->id == $question->id ? 'selected' : '' }}>
                                        Câu {{ $q->order_index }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="question-counter" id="questionCounter"></span>
                        </div>
                        <button id="nextQuestionBtn" class="nav-btn" onclick="navigateToNext()" title="Câu sau">
                            Sau →
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="layout">
            <aside class="info-panel">
                <div class="info-section">
                    <div class="info-title">Bài học</div>
                    <div class="info-text" id="infoLesson">Đang tải...</div>
                </div>
                <div class="info-section">
                    <div class="info-title">Chủ đề</div>
                    <div class="info-text" id="infoExercise">Đang tải...</div>
                </div>
                <div class="info-section">
                    <div class="info-title">Gợi ý</div>
                    <div class="info-highlight" id="infoHint">💡 Đang tải gợi ý...</div>
                </div>
                <div class="info-section">
                    <div class="info-title">Mẹo nhỏ</div>
                    <div class="info-text">Viết hoa chữ cái đầu câu, thêm dấu chấm ở cuối, và kiểm tra lại trước khi gửi.</div>
                </div>
            </aside>

            <main class="question-panel">
                <div class="question-header">
                    <h2 class="question-title" id="exerciseTitle">Đang tải...</h2>
                    <span class="lesson-pill" id="lessonPill">I-CLC</span>
                </div>

                <div class="prompt-card">
                    <h2>Đề bài</h2>
                    <p id="questionPrompt">Please wait...</p>
                    <div class="instruction-section" id="instructionSection" style="display: none;">
                        <div class="instruction-label">📝 Hướng dẫn:</div>
                        <div class="instruction-text" id="instructionText"></div>
                    </div>
                    <div class="hint-section" id="hintSection" style="display: none;">
                        <div class="hint-label">💡 Gợi ý:</div>
                        <div class="hint-text" id="hintText"></div>
                    </div>
                </div>

                <div class="answer-wrapper">
                    <div class="answer-label">✍️ Bé hãy viết câu trả lời</div>
                    <textarea 
                        id="userAnswer" 
                        placeholder="Viết câu trả lời của con tại đây..."
                        rows="5"
                    ></textarea>
                    <div class="btn-area">
                        <button class="btn-primary" id="submitBtn" onclick="submitAnswer()">
                            Gửi câu trả lời
                        </button>
                        <button class="btn-secondary" type="button" onclick="resetAnswer()">
                            Làm lại
                        </button>
                    </div>
                    <div class="loading" id="loading" style="display: none;">
                        <div class="spinner"></div>
                        <span>đang chấm bài của bạn...</span>
                    </div>
                    <div class="error" id="error">
                        ❌ Lỗi: <span id="errorMessage"></span>
                    </div>
                </div>

                <div class="result-card" id="resultCard">
                    <div class="result-feedback" id="resultFeedback"></div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/writing-question.js"></script>
@endpush
