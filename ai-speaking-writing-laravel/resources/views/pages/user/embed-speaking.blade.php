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
        /* Lock page - prevent scrolling */
        html, body {
            margin: 0;
            padding: 0;
            background: transparent;
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            position: fixed !important;
            box-sizing: border-box;
        }
        
        * {
            box-sizing: border-box;
        }
        
        #question-page-content {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100vw;
            padding: 16px;
            box-sizing: border-box;
        }
        
        /* Remove background shapes for cleaner iframe look */
        .question-page-wrapper .bg-shape {
            display: none;
        }
        
        /* Remove padding from question-page-wrapper */
        .question-page-wrapper {
            padding: 0;
            margin: 0;
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
            padding: 0;
            max-width: min(960px, 100%);
            width: 100%;
        }
        
        /* Hide navigation section */
        .nav-card,
        .nav-card-improved {
            display: none !important;
        }
        
        /* Question panel should take remaining space */
        .question-panel {
            width: 100%;
        }
        
        .answer-wrapper-container {
            width: 100%;
            overflow: hidden;
            padding: 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            max-height: 100%;
        }
        
        /* Increase font size for prompt */
        .prompt-card {
            padding: 16px 18px;
        }

        .prompt-card h2 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 6px;
        }

        .prompt-card p {
            font-size: 16px;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
        }
        
        /* Instruction toggle button - fixed position */
        .instruction-toggle-btn-fixed {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .instruction-toggle-btn-fixed:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        
        .instruction-icon {
            font-size: 20px;
            line-height: 1;
        }
        
        /* Instruction popup */
        .instruction-popup-content {
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .instruction-popup-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .instruction-icon-large {
            font-size: 32px;
            line-height: 1;
        }
        
        .instruction-popup-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .instruction-popup-body {
            padding: 0;
        }
        
        .instruction-popup-body .instruction-text {
            color: #4b5563;
            line-height: 1.8;
            font-size: 16px;
        }
        
        /* Remove padding from bottom navigation if exists */
        .bottom-navigation {
            padding: 0;
            margin-top: 8px;
        }

        .answer-wrapper-container.is-hidden {
            display: none !important;
        }

        .answer-wrapper--speaking {
            padding: 12px;
        }

        .answer-wrapper--speaking .speaking-layout {
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 0;
        }

        .speaking-illustration {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            border-radius: 12px;
            max-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            padding: 8px;
        }

        .speaking-illustration img {
            width: auto;
            max-width: 80%;
            height: auto;
            max-height: 110px;
            object-fit: contain;
            object-position: center;
            display: block;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .speaking-content {
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 0;
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
                display: grid;
                grid-template-columns: minmax(180px, 0.7fr) 1fr;
                align-items: center;
                gap: 20px;
            }

            .speaking-illustration,
            .speaking-content {
                min-height: 0;
            }
        }

        @media (max-width: 860px) {
            .speaking-illustration {
                display: none;
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

        .question-header {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .question-counter-banner {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.35);
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

            .question-counter-banner {
                width: 100%;
                justify-content: center;
            }
        }

        /* Popup Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }

        .popup-overlay.show {
            display: flex;
        }

        .popup-content {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: popupSlideIn 0.3s ease-out;
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .popup-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f3f4f6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #6b7280;
            transition: all 0.2s;
        }

        .popup-close:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .loading-popup-content {
            text-align: center;
            padding: 40px 24px;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e5e7eb;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 16px;
            color: #6b7280;
            font-weight: 500;
        }

        .result-popup-content {
            text-align: center;
        }

        .result-popup-content .status-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .result-popup-content .status-pill {
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
        }

        .result-popup-content .status-pill.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }

        .result-popup-content .status-pill.failure {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
        }

        .result-popup-content .score-pill {
            padding: 8px 16px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }

        .result-popup-content .result-text {
            text-align: left;
            margin: 16px 0;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            color: #1f2937;
            line-height: 1.6;
        }

        .result-popup-content .result-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 20px;
        }
        
        /* Bottom navigation styles */
        .bottom-navigation {
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 20px;
        }

        .bottom-navigation .nav-arrow-btn {
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
            transition: all 0.2s;
        }

        .bottom-navigation .nav-arrow-btn:hover:not(:disabled) {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .bottom-navigation .nav-arrow-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .bottom-navigation .nav-counter {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .result-popup-content .btn-primary {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: #3b82f6;
            color: #fff;
        }

        .result-popup-content .btn-primary:hover {
            background: #2563eb;
        }

        .result-popup-content .btn-secondary {
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px solid #3b82f6;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            color: #3b82f6;
        }

        .result-popup-content .btn-secondary:hover {
            background: #eff6ff;
        }
        
        /* Statistics Popup Styles */
        /* Lesson Statistics Section (not popup) */
        .lesson-statistics-section {
            margin-top: 24px;
            padding: 20px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-radius: 12px;
            border: 2px solid #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        
        .statistics-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .statistics-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .statistics-body {
            margin-bottom: 24px;
        }
        
        .statistics-summary {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .stat-value.highlight {
            color: #3b82f6;
            font-size: 24px;
        }
        
        .progress-bar-container {
            width: 100%;
            height: 12px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 999px;
            transition: width 0.3s ease;
        }
        
        .statistics-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
        }
        
        .statistics-actions .btn-secondary {
            padding: 12px 24px;
            border-radius: 8px;
            border: 2px solid #3b82f6;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            color: #3b82f6;
            font-size: 16px;
        }
        
        .statistics-actions .btn-secondary:hover {
            background: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
        }
        
        .statistics-body {
            margin-bottom: 0;
        }
        
        .statistics-summary {
            margin-bottom: 16px;
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

                <div class="answer-wrapper-container" id="writingAnswerWrapper" style="{{ $isWriting ? '' : 'display:none;' }}">
                    @include('components.user.writing-answer')
                </div>

                <div class="answer-wrapper-container" id="speakingAnswerWrapper" style="{{ $isWriting ? 'display:none;' : '' }}">
                    @include('components.user.speaking-answer', ['question' => $question])
                </div>

                <div class="bottom-navigation">
                    <button id="prevQuestionBtn" class="nav-arrow-btn" type="button" title="Câu trước">
                        <span class="nav-arrow-icon">←</span>
                        <span class="nav-arrow-text">Câu trước</span>
                    </button>
                    <span class="nav-counter" id="questionCounter"></span>
                    <button id="nextQuestionBtn" class="nav-arrow-btn" type="button" title="Câu sau">
                        <span class="nav-arrow-text">Câu sau</span>
                        <span class="nav-arrow-icon">→</span>
                    </button>
                </div>

                <!-- Lesson Statistics Section -->
                <div class="lesson-statistics-section" id="lessonStatisticsSection" style="display: none;">
                    <div id="lessonStatisticsContent"></div>
                </div>
            </main>
        </div>
    </div>

    <!-- Loading Popup -->
    <div class="popup-overlay" id="loadingPopup">
        <div class="popup-content loading-popup-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Đang chấm bài...</div>
        </div>
    </div>

    <!-- Result Popup -->
    <div class="popup-overlay" id="resultPopup">
        <div class="popup-content result-popup-content">
            <button class="popup-close" id="resultPopupClose">×</button>
            <div id="resultPopupContent"></div>
        </div>
    </div>

    <!-- Instruction Popup -->
    @if(!empty($exercise->instruction))
        <div class="popup-overlay" id="instructionPopup">
            <div class="popup-content instruction-popup-content">
                <button class="popup-close" id="instructionPopupClose">×</button>
                <div class="instruction-popup-header">
                    <span class="instruction-icon-large">📝</span>
                    <h3>Hướng dẫn</h3>
                </div>
                <div class="instruction-popup-body">
                    <div class="instruction-text" id="instructionText">{{ $exercise->instruction ?? '' }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Popup -->
    <div class="popup-overlay" id="statisticsPopup">
        <div class="popup-content statistics-popup-content">
            <button class="popup-close" id="statisticsPopupClose">×</button>
            <div id="statisticsPopupContent"></div>
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

