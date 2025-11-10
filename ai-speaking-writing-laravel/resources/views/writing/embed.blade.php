<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luyện Viết - I-CLC</title>
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
        
        /* Responsive for iframe */
        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
            }
            
            .info-panel {
                display: none;
            }
        }
    </style>

    @if (app()->environment('local'))
        <!-- Khi dev, load từ server Vite -->
        <link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">
        <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
    @else
        <!-- Khi build, load file đã compile -->
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
        <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
    @endif
</head>
<body>
    <div id="question-page-content" class="question-page-wrapper">
        <div class="container">
            <div class="header-card">
                <div class="header-content">
                    <h1 id="exerciseTitleHeading">Writing Exercise</h1>
                    <div class="tag-list">
                        <span class="tag">Loại bài: <strong id="typeTag">—</strong></span>
                        <span class="tag">Câu số <strong id="orderTag">#1</strong></span>
                    </div>
                    @if(isset($allExercises) && $allExercises->count() > 1)
                    <div class="exercise-selector-wrapper">
                        <label for="exerciseSelect" class="exercise-selector-label">Bài tập:</label>
                        <select id="exerciseSelect" class="exercise-select">
                            @foreach($allExercises as $ex)
                                <option value="{{ $ex->id }}" {{ $ex->id == $exercise->id ? 'selected' : '' }}>
                                    {{ $ex->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(isset($allQuestions) && $allQuestions->count() > 1)
                    <div class="question-navigation">
                        <div class="question-nav-controls">
                            <button id="prevQuestionBtn" class="nav-btn" title="Câu trước">
                                ← Trước
                            </button>
                            <div class="question-selector">
                                <label for="questionSelect" class="question-selector-label">Câu hỏi:</label>
                                <select id="questionSelect" class="question-select">
                                    @foreach($allQuestions as $q)
                                        <option value="{{ $q->id }}" {{ $q->id == $question->id ? 'selected' : '' }}>
                                            Câu {{ $q->order_index }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="question-counter" id="questionCounter"></span>
                            </div>
                            <button id="nextQuestionBtn" class="nav-btn" title="Câu sau">
                                Sau →
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

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
                    <div class="answer-input-group" id="wcsInputGroup" style="display: none;">
                        <div class="answer-prefix" id="wcsPrefix"></div>
                        <div class="answer-suffix-container">
                            <textarea 
                                id="answerSuffix" 
                                class="answer-suffix-input" 
                                rows="2"
                                placeholder="..."
                                autocomplete="off"
                            ></textarea>
                        </div>
                    </div>
                    <textarea 
                        id="userAnswer" 
                        placeholder="Viết câu trả lời của con tại đây..."
                        rows="5"
                    ></textarea>
                    <div class="btn-area">
                        <button class="btn-primary" id="submitBtn">
                            Gửi câu trả lời
                        </button>
                        <button class="btn-secondary" type="button">
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

    {{-- <script src="/js/writing-effects.js"></script>
    <script src="/js/writing-feedback.js"></script>
    <script src="/js/writing-question.js"></script> --}}
    {{-- <script>
        // Initialize when page loads (works in both iframe and standalone)
        document.addEventListener('DOMContentLoaded', function() {
            loadQuestion();
        });
    </script> --}}
</body>
</html>

