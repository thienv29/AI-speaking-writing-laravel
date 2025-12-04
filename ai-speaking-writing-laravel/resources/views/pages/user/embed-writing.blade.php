<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luyện viết - I-CLC</title>
    @include('components.user.embed-styles')
    
    @php
        $manifest = null;
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $json = file_get_contents($manifestPath);
            $manifest = $json ? json_decode($json, true) : null;
        }
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if ($cssFile)
        <link rel="stylesheet" href="{{ '/build/' . ltrim($cssFile, '/') }}">
    @endif
    @if ($jsFile)
        <script type="module" src="{{ '/build/' . ltrim($jsFile, '/') }}" defer></script>
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div id="question-page-content" class="question-page-wrapper embed-compact">
        <div class="container">
            @php
                $questionList = collect($allQuestions ?? []);
                $questionTotal = $questionList->count();
                if ($questionTotal === 0 && isset($exercise) && $exercise->relationLoaded('questions')) {
                    $questionTotal = $exercise->questions->count();
                }
                if ($questionTotal === 0) {
                    $questionTotal = 1;
                }
                $currentQuestionOrder = optional($questionList->firstWhere('id', $question->id))->order_index
                    ?? ($question->order_index ?? 1);
            @endphp
            
            @if(!empty($exercise->instruction))
                <button class="instruction-toggle-btn-fixed" id="instructionToggleBtn" type="button" title="Xem hướng dẫn">
                    <span class="instruction-icon">📝</span>
                </button>
            @endif
            
            <main class="question-panel">
                <div class="question-header">
                    <h2 class="question-title" id="exerciseTitle">Câu {{ $question->order_index ?? '—' }}</h2>
                    <span class="lesson-pill" id="lessonPill">{{ $lesson->title ?? 'I-CLC' }}</span>
                    <span class="question-counter-banner" id="questionCounterBanner">
                        Câu {{ $currentQuestionOrder }}/{{ $questionTotal }}
                    </span>
                </div>

                <div class="prompt-card">
                    <h2>Đề bài</h2>
                    <p id="questionPrompt">{{ $question->prompt_text ?? 'Please wait...' }}</p>
                </div>

                <div class="answer-wrapper-container" id="writingAnswerWrapper">
                    @include('components.user.writing-answer')
                </div>

                <div class="lesson-statistics-section" id="lessonStatisticsSection" style="display: none;">
                    <div id="lessonStatisticsContent"></div>
                </div>

                <div class="bottom-navigation">
                    <button id="prevQuestionBtn" class="nav-arrow-btn" type="button" title="Câu trước"
                        @if(!$question->prev_question_id) disabled @endif
                        data-prev-id="{{ $question->prev_question_id ?? null }}">
                        <span class="nav-arrow-icon">←</span>
                        <span class="nav-arrow-text">Câu trước</span>
                    </button>
                    <span class="nav-counter" id="questionCounter"></span>
                    <button id="nextQuestionBtn" class="nav-arrow-btn" type="button" title="Câu sau"
                        @if(!$question->next_question_id) disabled @endif
                        data-next-id="{{ $question->next_question_id ?? null }}">
                        <span class="nav-arrow-text">Câu sau</span>
                        <span class="nav-arrow-icon">→</span>
                    </button>
                </div>
            </main>
        </div>
    </div>

    @include('components.user.embed-popups', ['exercise' => $exercise])

    <script>
        window.appData = {
            question: @json($question),
            navigation: @json($navigationData ?? []),
            current: @json($currentContext ?? []),
            allQuestions: @json($allQuestions ?? [])
        };
    </script>
</body>
</html>
