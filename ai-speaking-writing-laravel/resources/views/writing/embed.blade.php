<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luyện Viết - I-CLC</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/writing-question.css">
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
</head>
<body>
    <div class="question-page-wrapper">
        <div class="container">
            <div class="header-card">
                <div class="header-content">
                    <h1 id="exerciseTitleHeading">Writing Exercise</h1>
                    <div class="tag-list">
                        <span class="tag">Loại bài: <strong id="typeTag">—</strong></span>
                        <span class="tag">Câu số <strong id="orderTag">#1</strong></span>
                    </div>
                    @if(isset($allQuestions) && $allQuestions->count() > 1)
                    <div class="question-selector">
                        <label for="questionSelect" class="question-selector-label">Chọn câu hỏi:</label>
                        <select id="questionSelect" class="question-select" onchange="navigateToQuestion(this.value)">
                            @foreach($allQuestions as $q)
                                <option value="{{ $q->id }}" {{ $q->id == $question->id ? 'selected' : '' }}>
                                    Câu {{ $q->order_index }}
                                </option>
                            @endforeach
                        </select>
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

    <script src="/js/writing-question.js"></script>
    <script>
        // Initialize when page loads (works in both iframe and standalone)
        document.addEventListener('DOMContentLoaded', function() {
            loadQuestion();
        });
    </script>
</body>
</html>

