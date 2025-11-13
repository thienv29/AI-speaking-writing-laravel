@extends('layouts.app')

@section('meta')
<title>Writing Adventure | I-CLC</title>
@endsection

@section('content')
<div id="question-page-content" class="question-page-wrapper">
    <div class="bg-shape one"></div>
    <div class="bg-shape two"></div>

    <div class="container">
        <div class="back-link">
            <a href="{{ route('user.home') }}" class="back-button">🏠 Trang chủ</a>
            <a href="{{ route('user.lesson', ['id' => $question->exercise->lesson->id]) }}" class="back-button">← Quay lại danh sách bài tập</a>
        </div>

        <div class="header-card">
            <div class="header-illustration">
                @if (Str::startsWith($question->exercise->type->code, 'W'))
                    ✍️
                @else
                    🎙️
                @endif
            </div>
            <div class="header-content">
                <span class="tagline" id="lessonBadge">{{ $question->exercise->lesson->title }}</span>
                <h1><span id="exerciseTitleHeading">{{ $question->exercise->title }}</span></h1>
                <p class="header-sub" id="exerciseSubtitle">{{ $question->exercise->instruction }}</p>
                <div class="tag-list">
                    {{-- <span class="tag">Template: <strong id="templateTag">—</strong></span> --}}
                    <span class="tag">Loại bài: <strong id="typeTag">{{ $question->exercise->type->code }}</strong></span>
                    <span class="tag">Câu số <strong id="orderTag"># {{ $question->order_index . '' }}</strong></span>
                </div>
                <div class="question-navigation">
                    <div class="question-nav-controls">
                        <a id="prevQuestionBtn" class="nav-btn opacity-0 pointer-events-none" title="Câu trước">
                            ← Trước
                        </a>
                        <div class="question-selector">
                            <label for="questionSelect" class="question-selector-label">Câu hỏi:</label>
                            <select id="questionSelect" class="question-select">
                                @foreach($question->exercise->questions as $q)
                                    <option value="{{ $q->id }}" {{ $q->id == $question->id ? 'selected' : '' }}>
                                        Câu {{ $q->order_index }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="question-counter" id="questionCounter"></span>
                        </div>
                        <a id="nextQuestionBtn" class="nav-btn opacity-0 pointer-events-none" title="Câu sau">
                            Sau →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="layout">
            <aside class="info-panel">
                <div class="info-section">
                    <div class="info-title">Bài học</div>
                    <div class="info-text" id="infoLesson">{{ $question->exercise->lesson->title }}</div>
                </div>
                <div class="info-section">
                    <div class="info-title">Chủ đề</div>
                    <div class="info-text" id="infoExercise">
                        @if (Str::startsWith($question->exercise->type->code, 'W'))
                            Bài tập viết
                        @else
                            Bài tập đọc
                        @endif
                    </div>
                </div>
                <div class="info-section">
                    <div class="info-title">Gợi ý</div>
                    <div class="info-highlight" id="infoHint">💡 {{ $question->exercise->instruction }}</div>
                </div>
                <div class="info-section">
                    <div class="info-title">Mẹo nhỏ</div>
                    <div class="info-text">
                        @if (Str::startsWith($question->exercise->type->code, 'W'))
                            Viết hoa chữ cái đầu câu, thêm dấu chấm ở cuối, và kiểm tra lại trước khi gửi.
                        @else
                            Đọc to và rõ ràng, nên tránh môi trường nhiều tiếng ồn. 
                        @endif
                    </div>
                </div>
            </aside>

            <main class="question-panel">
                <div class="question-header">
                    <h2 class="question-title" id="question-title">
                        Câu {{ $question->order_index . ''}}</h2>
                    <span class="lesson-pill" id="lessonPill">{{ $question->exercise->lesson->title }}</span>
                </div>

                <div class="prompt-card">
                    <h2>Đề bài</h2>
                    <p id="questionPrompt">{{ $question->prompt_text }}</p>
                    <div class="instruction-section" id="instructionSection">
                        <div class="instruction-label">📝 Hướng dẫn:</div>
                        <div class="instruction-text" id="instructionText">{{ $question->exercise->instruction }}</div>
                    </div>
                    {{-- <div class="hint-section" id="hintSection" style="display: none;">
                        <div class="hint-label">💡 Gợi ý:</div>
                        <div class="hint-text" id="hintText"></div>
                    </div> --}}
                </div>

                @if (Str::startsWith($question->exercise->type->code, 'W'))
                    @include('components.user.writing-answer')
                @else
                    @include('components.user.speaking-answer', ['question' => $question])
                @endif

                <div class="result-card" id="resultCard">
                    <div class="result-feedback" id="resultFeedback"></div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.appData = {
        question: @json($question)
    };
</script>
@endpush