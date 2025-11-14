<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $typeCode = strtoupper($exerciseType->code ?? '');
        $isWriting = str_starts_with($typeCode, 'W');
        $isSpeaking = str_starts_with($typeCode, 'S');
    @endphp
    <title>{{ $isSpeaking ? 'Luyện Nói' : 'Luyện Viết' }} - I-CLC</title>
    {{-- <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/writing-question.css"> --}}
    <style>
        /* Reset body for iframe */
        body {
            margin: 0;
            padding: 0;
            background: transparent;
        }
        
        /* Remove background shapes for cleaner iframe look */
        .question-page-wrapper .bg-shape {
            display: none;
        }
        
        /* Compact header for iframe */
        .header-card {
            margin-bottom: 20px;
            padding: 16px 20px;
        }
        
        .header-card h1 {
            font-size: 20px;
        }
        
        /* Compact layout */
        .layout {
            gap: 16px;
        }
        
        /* Hide back links in iframe */
        .back-link {
            display: none;
        }
        
        /* Adjust container padding */
        .question-page-wrapper .container {
            padding: 16px;
            max-width: 100%;
        }
        
        .answer-wrapper-container {
            width: 100%;
        }

        .answer-wrapper-container.is-hidden {
            display: none !important;
        }

        .answer-wrapper--speaking .speaking-layout {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .speaking-illustration img {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .speaking-content {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .speaking-prompt {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .speaking-prompt p {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .speaking-recorder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .speaking-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 999px;
            border: none;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }

        .speaking-btn:active {
            transform: scale(0.97);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .speaking-btn--audio {
            background: linear-gradient(135deg, #38bdf8, #2563eb);
        }

        .speaking-btn--mic {
            background: linear-gradient(135deg, #f87171, #dc2626);
        }

        .speaking-indicator {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 6px solid rgba(239, 68, 68, 0.3);
            border-top-color: rgba(239, 68, 68, 0.9);
            animation: speaking-spin 1s linear infinite;
        }

        .speaking-indicator.hidden {
            display: none;
        }

        .speaking-audio.hidden {
            display: none;
        }

        @keyframes speaking-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (min-width: 768px) {
            .answer-wrapper--speaking .speaking-layout {
                flex-direction: row;
                align-items: stretch;
            }

            .speaking-illustration,
            .speaking-content {
                flex: 1;
            }
        }

        /* Improved Navigation Layout */
        .nav-card-improved {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 24px;
            align-items: center;
            padding: 20px 24px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-top: 16px;
        }

        .nav-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-section--lesson {
            text-align: left;
        }

        .nav-section--question {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 16px;
            min-width: 320px;
        }

        .nav-section--type {
            text-align: right;
        }

        .nav-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .nav-select-wrapper {
            position: relative;
            width: 100%;
            max-width: 250px;
        }

        .nav-section--lesson .nav-select-wrapper {
            max-width: 250px;
            margin-right: auto;
        }

        .nav-section--type .nav-select-wrapper {
            max-width: 250px;
            margin-left: auto;
        }

        .nav-select {
            width: 100%;
            padding: 10px 36px 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .nav-select:hover {
            border-color: #3b82f6;
        }

        .nav-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .nav-select-wrapper::after {
            content: '▼';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6b7280;
            font-size: 10px;
        }

        .nav-question-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .nav-question-group .nav-label {
            margin-bottom: 0;
        }

        .nav-question-group .nav-select-wrapper {
            max-width: 120px;
        }

        .nav-counter {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
        }

        .nav-arrow-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nav-arrow-btn:hover:not(:disabled) {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .nav-arrow-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .nav-arrow-icon {
            font-size: 16px;
        }

        .nav-select-static {
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            background: #f9fafb;
        }

        /* Responsive for iframe */
        @media (max-width: 1024px) {
            .nav-card-improved {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .nav-section--lesson,
            .nav-section--type {
                text-align: center;
            }

            .nav-section--lesson .nav-select-wrapper,
            .nav-section--type .nav-select-wrapper {
                margin: 0 auto;
            }

            .nav-section--question {
                order: -1;
                min-width: 100%;
                padding-bottom: 16px;
                border-bottom: 1px solid #e5e7eb;
            }
        }

        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
            }
            
            .info-panel {
                display: none;
            }

            .nav-section--question {
                flex-direction: column;
                gap: 12px;
            }

            .nav-arrow-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

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
    $navigationCollection = collect($navigationData ?? []);
@endphp
            <div class="header-card">
                <div class="header-content">
                    <h1 id="exerciseTitleHeading">{{ $exercise->title ?? ($isSpeaking ? 'Speaking Exercise' : 'Writing Exercise') }}</h1>
                    <div class="tag-list">
                        <span class="tag">Loại bài: <strong id="typeTag">{{ $exerciseType->code ?? '—' }}</strong></span>
                        <span class="tag">Câu số <strong id="orderTag">#{{ $question->order_index ?? '1' }}</strong></span>
                    </div>
                    <div class="nav-card nav-card-improved">
                        <!-- Bài học - Bên trái -->
                        <div class="nav-section nav-section--lesson">
                            <span class="nav-label">Bài học</span>
                            @php
                                // Collect all unique lessons from all types
                                $allLessonsMap = [];
                                foreach($navigationCollection as $typeGroup) {
                                    foreach($typeGroup['lessons'] ?? [] as $lessonGroup) {
                                        $lessonId = $lessonGroup['id'];
                                        if (!isset($allLessonsMap[$lessonId])) {
                                            $allLessonsMap[$lessonId] = $lessonGroup;
                                        }
                                    }
                                }
                                $allLessons = array_values($allLessonsMap);
                            @endphp
                            @if(count($allLessons) > 0)
                                <div class="nav-select-wrapper">
                                    <select id="lessonSelect" class="nav-select">
                                        @foreach($allLessons as $lessonItem)
                                            <option value="{{ $lessonItem['id'] }}"
                                                {{ $lessonItem['id'] == ($lesson->id ?? null) ? 'selected' : '' }}>
                                                {{ $lessonItem['title'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="nav-select-static">
                                    {{ $lesson->title ?? 'Bài học' }}
                                </div>
                            @endif
                        </div>

                        <!-- Câu hỏi - Giữa -->
                        <div class="nav-section nav-section--question">
                            <button id="prevQuestionBtn" class="nav-arrow-btn" type="button" title="Câu trước">
                                <span class="nav-arrow-icon">←</span>
                                <span class="nav-arrow-text">Trước</span>
                            </button>

                            <div class="nav-question-group">
                                <span class="nav-label">CÂU HỎI</span>
                                <div class="nav-select-wrapper">
                                    <select id="questionSelect" class="nav-select">
                                        @if(isset($allQuestions))
                                            @foreach($allQuestions as $q)
                                                <option value="{{ $q->id }}" {{ $q->id == $question->id ? 'selected' : '' }}>
                                                    Câu {{ $q->order_index }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="nav-counter" id="questionCounter"></div>
                            </div>

                            <button id="nextQuestionBtn" class="nav-arrow-btn" type="button" title="Câu sau">
                                <span class="nav-arrow-text">Sau</span>
                                <span class="nav-arrow-icon">→</span>
                            </button>
                        </div>

                        <!-- Dạng bài - Bên phải -->
                        <div class="nav-section nav-section--type">
                            <span class="nav-label">Dạng bài</span>
                            @if($navigationCollection->count() > 0)
                                <div class="nav-select-wrapper">
                                    <select id="typeSelect" class="nav-select">
                                        @foreach($navigationCollection as $typeGroup)
                                            <option value="{{ $typeGroup['code'] }}" {{ ($exerciseType->code ?? '') === $typeGroup['code'] ? 'selected' : '' }}>
                                                {{ $typeGroup['name'] ?? $typeGroup['code'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="nav-select-static">
                                    {{ $exerciseType->name ?? $exerciseType->code ?? 'Dạng bài' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <main class="question-panel">
                <div class="question-header">
                    <h2 class="question-title" id="exerciseTitle">Câu {{ $question->order_index ?? '—' }}</h2>
                    <span class="lesson-pill" id="lessonPill">{{ $lesson->title ?? 'I-CLC' }}</span>
                </div>

                <div class="prompt-card">
                    <h2>Đề bài</h2>
                    <p id="questionPrompt">{{ $question->prompt_text ?? 'Please wait...' }}</p>
                    <div class="instruction-section" id="instructionSection" style="display: {{ !empty($exercise->instruction) ? 'block' : 'none' }};">
                        <div class="instruction-label">📝 Hướng dẫn:</div>
                        <div class="instruction-text" id="instructionText">{{ $exercise->instruction ?? '' }}</div>
                    </div>
                    <div class="hint-section" id="hintSection" style="display: {{ !empty($question->starter_text) ? 'block' : 'none' }};">
                        <div class="hint-label">💡 Gợi ý:</div>
                        <div class="hint-text" id="hintText">{{ $question->starter_text ?? '' }}</div>
                    </div>
                </div>

                <div class="answer-wrapper-container" id="writingAnswerWrapper" style="{{ $isWriting ? '' : 'display:none;' }}">
                    @include('components.user.writing-answer')
                </div>

                <div class="answer-wrapper-container" id="speakingAnswerWrapper" style="{{ $isWriting ? 'display:none;' : '' }}">
                    @include('components.user.speaking-answer', ['question' => $question])
                </div>

                <div class="result-card" id="resultCard">
                    <div class="result-feedback" id="resultFeedback"></div>
                </div>
            </main>
        </div>
    </div>

    <script>
        window.appData = {
            question: @json($question),
            navigation: @json($navigationData ?? []),
            current: @json($currentContext ?? [])
        };
    </script>
</body>
</html>

