<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writing Adventure | I-CLC</title>
    <style>
        :root {
            --orange: #f89b1c;
            --orange-soft: #ffe3b0;
            --yellow: #f5c12c;
            --pink: #f47fb3;
            --coral: #ffbd59;
            --green: #2ecc71;
            --green-soft: #d9f5e4;
            --blue: #3c69e7;
            --violet: #6b2c90;
            --navy: #1f2a56;
            --text: #2e2f4b;
            --bg: #f7f4ff;
            --card-bg: rgba(255,255,255,0.96);
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(130deg, #f7f4ff 0%, #fff8ef 45%, #eef3ff 100%);
            color: var(--text);
            padding: 0 20px;
            position: relative;
            overflow-x: hidden;
        }

        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            z-index: 0;
        }
        .bg-shape.one {
            width: 260px;
            height: 260px;
            background: var(--coral);
            top: -60px;
            left: -80px;
        }
        .bg-shape.two {
            width: 320px;
            height: 320px;
            background: var(--blue);
            bottom: -120px;
            right: -80px;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 80px 0;
            position: relative;
            z-index: 1;
        }

        .header-card {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 32px 36px;
            box-shadow: 0 18px 45px rgba(107, 44, 144, 0.18);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 24px;
            position: relative;
        }

        .header-illustration {
            width: 160px;
            height: 160px;
            border-radius: 22px;
            background: radial-gradient(circle at 30% 30%, #fff6ff, #f5d6ff 70%, #ffdcae 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 78px;
            box-shadow: inset 0 0 0 6px rgba(255,255,255,0.6);
            position: relative;
        }

        .header-content {
            flex: 1 1 300px;
        }

        .header-sub {
            margin: 0;
            font-size: 15px;
            color: #5f6f7d;
            line-height: 1.6;
        }

        .back-link {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            margin-bottom: 22px;
        }
        .back-button {
            background: rgba(107, 44, 144, 0.1);
            color: var(--violet);
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
        }
        .back-button:hover {
            background: rgba(107, 44, 144, 0.18);
        }

        .header-content h1 {
            font-size: 36px;
            color: var(--navy);
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-content h1 span {
            background: linear-gradient(90deg, var(--violet), var(--orange));
            -webkit-background-clip: text;
            color: transparent;
        }

        .header-content p {
            margin: 0;
            font-size: 16px;
            color: #5f6f7d;
            line-height: 1.6;
        }

        .tagline {
            display: inline-block;
            background: rgba(107, 44, 144, 0.1);
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(107, 44, 144, 0.18);
            font-size: 13px;
            color: var(--violet);
            margin-bottom: 14px;
        }

        .tag {
            background: rgba(107,44,144,0.08);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            color: var(--violet);
            border: 1px solid rgba(107,44,144,0.18);
        }

        .layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 28px;
            margin-top: 36px;
        }

        .info-panel {
            background: rgba(255,255,255,0.92);
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(107, 44, 144, 0.18);
        }

        .info-section {
            margin-bottom: 22px;
        }

        .info-section:last-child { margin-bottom: 0; }

        .info-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .info-text {
            font-size: 14px;
            color: #5f6f7d;
            line-height: 1.6;
        }

        .info-highlight {
            background: rgba(248, 155, 28, 0.12);
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 14px;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }

        .question-panel {
            background: rgba(255,255,255,0.95);
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(255, 146, 105, 0.2);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .question-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--navy);
            margin: 0;
        }

        .lesson-pill {
            background: linear-gradient(135deg, rgba(255, 203, 153, 0.8), rgba(255, 153, 204, 0.8));
            color: var(--navy);
            border-radius: 999px;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 13px;
        }

        .prompt-card {
            background: linear-gradient(120deg, rgba(255, 249, 236, 0.9), rgba(255, 236, 247, 0.9));
            border: 1px solid rgba(255, 173, 145, 0.3);
            border-radius: 20px;
            padding: 22px;
            margin-bottom: 24px;
            position: relative;
        }

        .prompt-card::before {
            content: '❓';
            position: absolute;
            top: -18px;
            left: 24px;
            background: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(255, 183, 129, 0.25);
            font-size: 20px;
        }

        .prompt-card h2 {
            margin: 0 0 8px;
            font-size: 18px;
            color: var(--navy);
        }

        .prompt-card p {
            margin: 0;
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .answer-wrapper {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(52, 152, 219, 0.2);
            border-radius: 20px;
            padding: 20px;
        }

        .answer-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 12px;
        }

        textarea {
            width: 100%;
            min-height: 140px;
            padding: 16px 18px;
            border: 2px solid rgba(52, 152, 219, 0.15);
            border-radius: 16px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            resize: vertical;
            background: #fffefb;
            box-shadow: inset 0 3px 8px rgba(0,0,0,0.03);
        }

        textarea:focus {
            outline: none;
            border-color: rgba(243, 156, 18, 0.6);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.2);
        }

        .btn-area {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
            align-items: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--pink));
            color: white;
            padding: 12px 26px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 12px 28px rgba(255, 149, 128, 0.4);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-primary:hover { transform: translateY(-2px); }
        .btn-primary:active { transform: translateY(1px); }
        .btn-primary:disabled {
            background: #ccc;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: rgba(52, 152, 219, 0.12);
            color: var(--blue);
            padding: 10px 20px;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
        }

        .result-card {
            margin-top: 26px;
            background: rgba(255,255,255,0.95);
            border-radius: 24px;
            border: 1px solid rgba(46, 204, 113, 0.25);
            box-shadow: 0 20px 48px rgba(46, 204, 113, 0.25);
            padding: 28px;
            display: none;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 60px rgba(46, 204, 113, 0.28);
        }
        .result-card.anim-shake { animation: shake 0.6s; }
        .result-card.anim-bounce { animation: bounce 0.8s; }
        .result-card.anim-pulse { animation: pulse 1.2s; }
        .result-card.anim-wave::after {
            content: '👋';
            position: absolute;
            top: -10px;
            right: 10px;
            font-size: 32px;
            animation: wave 1s ease-in-out 2;
        }
        .result-card.anim-sparkle::after {
            content: '✨';
            position: absolute;
            top: -10px;
            left: 12px;
            font-size: 28px;
            animation: sparkle 1.4s ease-in-out 2;
        }
        .result-card.anim-rainbow {
            background: linear-gradient(120deg, rgba(255,255,255,0.92), rgba(255,240,245,0.9));
            box-shadow: 0 0 12px rgba(255, 182, 193, 0.45);
        }
        .result-card.anim-confetti::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            background-image:
                radial-gradient(#f39c12 2px, transparent 3px),
                radial-gradient(#8e44ad 2px, transparent 3px),
                radial-gradient(#e74c3c 2px, transparent 3px);
            background-size: 12px 12px;
            background-position: 10px 10px, 40px 30px, 70px 50px;
            opacity: 0;
            animation: confetti 1s ease-out forwards;
        }
        .celebration-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            overflow: hidden;
            z-index: 999;
        }
        .confetti-piece {
            position: absolute;
            width: 12px;
            height: 16px;
            border-radius: 3px;
            opacity: 0;
            animation: confetti-fall 3s ease-in forwards;
        }
        .star-burst {
            position: absolute;
            font-size: 24px;
            opacity: 0;
            animation: star-burst 1.1s ease-out forwards;
        }
        .result-title {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }
        .result-feedback {
            color: #555;
            line-height: 1.6;
        }
        .status-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
        }
        .status-pill.success { background: rgba(46, 204, 113, 0.18); color: var(--green); }
        .status-pill.failure { background: rgba(231, 76, 60, 0.18); color: #c0392b; }
        .score-pill {
            background: rgba(60, 105, 231, 0.12);
            color: var(--blue);
            border-radius: 999px;
            padding: 6px 12px;
            font-weight: 600;
        }
        .result-text {
            margin-top: 14px;
            color: #5f6f7d;
            line-height: 1.6;
        }
        .result-actions {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-secondary {
            background: rgba(60, 105, 231, 0.12);
            color: var(--blue);
            padding: 10px 22px;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-secondary:hover {
            background: rgba(60, 105, 231, 0.18);
        }
        .effect-message {
            margin-top: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        .hl-container {
            font-size: 16px;
            background: #fff;
            padding: 10px 12px;
            border: 1px dashed #ddd;
            border-radius: 6px;
            margin-top: 10px;
            white-space: pre-wrap;
        }
        .hl-notes {
            margin: 10px 0 0;
            padding-left: 18px;
            color: #5f6f7d;
        }
        .hl-notes li { margin-bottom: 4px; }
        .hl-correct,
        .hl-wrong,
        .hl-neutral {
            padding: 2px 0;
        }
        .hl-correct {
            box-shadow: inset 0 -0.6em rgba(39, 174, 96, 0.35);
        }
        .hl-wrong {
            box-shadow: inset 0 -0.6em rgba(231, 76, 60, 0.35);
        }
        .hl-neutral {
            box-shadow: inset 0 -0.6em rgba(52, 152, 219, 0.25);
        }
        @keyframes shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-6px); }
            40%, 60% { transform: translateX(6px); }
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-12px); }
            60% { transform: translateY(-6px); }
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(39, 174, 96, 0.4); }
            70% { box-shadow: 0 0 0 14px rgba(39, 174, 96, 0); }
            100% { box-shadow: 0 0 0 0 rgba(39, 174, 96, 0); }
        }
        @keyframes wave {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(20deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes sparkle {
            0%, 100% { opacity: 0; transform: scale(0.6); }
            50% { opacity: 1; transform: scale(1.1); }
        }
        @keyframes confetti {
            0% { opacity: 0; transform: translateY(-10px); }
            30% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(20px); }
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-20px) rotate(0deg); opacity: 0; }
            15% { opacity: 1; }
            100% { transform: translateY(120px) rotate(360deg); opacity: 0; }
        }
        @keyframes star-burst {
            0% { transform: scale(0.2); opacity: 0; }
            40% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.6); opacity: 0; }
        }
        .loading {
            display: none;
            text-align: center;
            color: #666;
            margin-top: 15px;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
            padding: 15px;
            margin-top: 15px;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="bg-shape one"></div>
    <div class="bg-shape two"></div>

    <div class="container">
        <div class="back-link">
            <a href="/" class="back-button">🏠 Trang chủ</a>
            <a href="/writing" class="back-button">← Quay lại danh sách</a>
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
                    <div class="loading" id="loading">
                        ⏳ Hệ thống đang chấm bài...
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

    <script>
        let currentQuestionId = null;
        let questionEffects = {};
        let exerciseEffects = {};
        const audioCache = {};
        let audioContext = null;
        const fallbackToneMap = {
            success: 880,
            failure: 330,
            button: 520,
        };
        const animationClassMap = {
            confetti: 'anim-confetti',
            shake: 'anim-shake',
            wobble: 'anim-shake',
            bounce: 'anim-bounce',
            sparkle: 'anim-sparkle',
            wave: 'anim-wave',
            pulse: 'anim-pulse',
            rainbow: 'anim-rainbow',
            tick: 'anim-pulse'
        };
        
        // Load question data
        async function loadQuestion() {
            try {
                // Get question ID from URL
                const pathParts = window.location.pathname.split('/');
                currentQuestionId = pathParts[pathParts.length - 1];
                
                // Load question from API
                const response = await fetch(`/api/questions/${currentQuestionId}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const question = data.data;
                    const exercise = question.exercise || {};
                    const exerciseType = exercise.type || {};
                    const lesson = exercise.lesson || {};
                    const lessonTitle = lesson.title || 'I-CLC Lesson';

                    document.getElementById('exerciseTitleHeading').textContent = exercise.title || 'Writing Exercise';
                    document.getElementById('exerciseTitle').textContent = exercise.title || 'Writing Exercise';
                    document.getElementById('lessonPill').textContent = lessonTitle;
                    document.getElementById('lessonBadge').textContent = `Bài học: ${lessonTitle}`;
                    document.getElementById('infoLesson').textContent = lessonTitle;
                    document.getElementById('infoExercise').textContent = exerciseType.name || 'Writing Practice';
                    document.getElementById('typeTag').textContent = exerciseType.code || exerciseType.name || '—';
                    document.getElementById('templateTag').textContent = ((question.effect && question.effect.template) || 'general').toUpperCase();
                    document.getElementById('orderTag').textContent = `#${question.order_index ?? '?'}`;
                    const promptText = question.prompt_text || (question.starter_text ? `${question.starter_text} ...` : '—');
                    document.getElementById('questionPrompt').textContent = promptText;
                    document.getElementById('exerciseSubtitle').textContent = buildSubtitle(question);

                    questionEffects = question.effect || {};
                    exerciseEffects = questionEffects.exercise || {};
                    applyExerciseBackground();
                    loadTemplateHint(currentQuestionId);
                } else {
                    throw new Error('Failed to load question');
                }
            } catch (error) {
                showError('Không thể tải câu hỏi: ' + error.message);
            }
        }
        
        
        // Submit answer
        async function submitAnswer() {
            const answer = document.getElementById('userAnswer').value.trim();
            
            if (!answer) {
                showError('Vui lòng viết câu trả lời trước khi gửi.');
                return;
            }
            
            if (!currentQuestionId) {
                showError('ID câu hỏi không tìm thấy.');
                return;
            }
            
            // Button sound & loading
            playSound(exerciseEffects.button_sound, 'button');

            document.getElementById('submitBtn').disabled = true;
            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            document.getElementById('resultCard').style.display = 'none';
            
            try {
                const response = await fetch('/api/attempts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        question_id: parseInt(currentQuestionId),
                        user_id: 1,
                        user_answer: answer
                    })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    showResult(data.data);
                } else {
                    showError(data.message || 'Không thể đánh giá câu trả lời');
                }
            } catch (error) {
                showError('Lỗi mạng: ' + error.message);
            } finally {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('loading').style.display = 'none';
            }
        }

        function buildSubtitle(question) {
            const template = (question.effect && question.effect.template) || '';
            if (question.starter_text) {
                return `Bé hãy viết tiếp câu bắt đầu bằng “${question.starter_text}…” nhé!`;
            }
            switch (template) {
                case 'name':
                    return 'Giới thiệu tên của con bằng câu “My name is …”.';
                case 'age':
                    return 'Nói tuổi của con bằng câu “I am [number] years old.”';
                case 'location':
                    return 'Hãy kể con đang sống ở đâu bằng câu “I live in …”.';
                case 'time':
                    return 'Dùng câu “It is [number] o\'clock.” để nói giờ hiện tại nhé.';
                case 'weather':
                    return 'Mô tả thời tiết bằng câu “Today is …”.';
                case 'hobby':
                    return 'Chia sẻ sở thích của con bằng câu “My favorite hobby is …”.';
                default:
                    return 'Viết câu trả lời đầy đủ, nhớ viết hoa chữ cái đầu và kết thúc bằng dấu chấm nhé!';
            }
        }

        async function loadTemplateHint(questionId) {
            const hintBox = document.getElementById('infoHint');
            try {
                const res = await fetch(`/api/questions/${questionId}/template-hint`);
                const data = await res.json();
                if (data.status === 'success' && data.data && data.data.hint) {
                    hintBox.textContent = `💡 ${data.data.hint}`;
                } else {
                    hintBox.textContent = '💡 Hãy cố gắng viết đúng cấu trúc đã học nhé!';
                }
            } catch (error) {
                hintBox.textContent = '💡 Không lấy được gợi ý, con hãy thử dựa vào đề bài nhé!';
            }
        }

        function resetAnswer() {
            const answerField = document.getElementById('userAnswer');
            answerField.value = '';
            answerField.focus();
            document.getElementById('error').style.display = 'none';
            document.getElementById('resultCard').style.display = 'none';
        }
        
        // Show result
        function showResult(data) {
            const resultCard = document.getElementById('resultCard');
            const resultFeedback = document.getElementById('resultFeedback');
            const effectData = data.effect || {};
            const stage = data.is_correct ? 'success' : 'failure';
            const stageEffect = getStageEffect(effectData, stage);
            const effectMessage = stageEffect && stageEffect.message ? stageEffect.message : '';

            const statusText = data.is_correct ? 'Làm tốt lắm!' : 'Cùng thử lại nhé';
            const statusClass = data.is_correct ? 'success' : 'failure';
            const score = data.score ? `${data.score}/100` : (data.feedback.includes('Điểm:') ? data.feedback.split('Điểm:')[1].split('/')[0] + '/100' : 'N/A');

            const highlightHtml = renderHighlights(data);
            const notesHtml = renderNotes(data);

            const feedbackText = escapeHtml(data.feedback);

            resultFeedback.innerHTML = `
                <div class="status-row">
                    <span class="status-pill ${statusClass}">✨ ${statusText}</span>
                    <span class="score-pill">Điểm: ${score}</span>
                </div>
                <div class="result-text">${feedbackText}</div>
                ${effectMessage ? `<div class="effect-message">${escapeHtml(effectMessage)}</div>` : ''}
                ${highlightHtml || notesHtml ? `<div class="hl-container">${highlightHtml}${notesHtml}</div>` : ''}
                <div class="result-actions">
                    <button class="btn-primary" type="button" onclick="resetAnswer()">Làm thêm lần nữa</button>
                    <a href="/writing" class="btn-secondary" style="text-decoration:none;">Chọn bài khác</a>
                </div>
            `;

            resultCard.style.display = 'block';
            resultCard.scrollIntoView({ behavior: 'smooth' });
            applyEffect(stageEffect, stage);
            if (stageEffect && stageEffect.sound) {
                playSound(stageEffect.sound, stage);
            }
        }
        function getStageEffect(effectData, stage) {
            if (effectData && effectData[stage]) {
                return effectData[stage];
            }
            if (questionEffects.effects && questionEffects.effects[stage]) {
                return questionEffects.effects[stage];
            }
            return null;
        }

        function playSound(url, stage) {
            if (!url) return;
            try {
                let audio = audioCache[url];
                if (!audio) {
                    audio = new Audio(url);
                    audioCache[url] = audio;
                }
                audio.currentTime = 0;
                audio.play().catch(() => playFallbackTone(stage));
            } catch (error) {
                console.warn('Unable to play sound', error);
                playFallbackTone(stage);
            }
        }

        function ensureAudioContext() {
            if (audioContext) return audioContext;
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return null;
            audioContext = new AudioCtx();
            return audioContext;
        }

        function playFallbackTone(stage) {
            const ctx = ensureAudioContext();
            if (!ctx) return;

            const freq = fallbackToneMap[stage] || 440;
            const duration = stage === 'failure' ? 0.35 : 0.18;

            const oscillator = ctx.createOscillator();
            const gainNode = ctx.createGain();

            oscillator.type = stage === 'failure' ? 'sawtooth' : 'sine';
            oscillator.frequency.setValueAtTime(freq, ctx.currentTime);

            gainNode.gain.setValueAtTime(0.001, ctx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.02);
            gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);

            oscillator.connect(gainNode);
            gainNode.connect(ctx.destination);

            oscillator.start(ctx.currentTime);
            oscillator.stop(ctx.currentTime + duration + 0.05);
        }

        function applyExerciseBackground() {
            const panel = document.querySelector('.question-panel');
            const infoPanel = document.querySelector('.info-panel');
            if (!exerciseEffects || !exerciseEffects.background) {
                panel.style.background = 'rgba(255,255,255,0.95)';
                panel.style.backgroundSize = '';
                infoPanel.style.background = 'rgba(255,255,255,0.92)';
                return;
            }

            switch (exerciseEffects.background) {
                case 'stars':
                    panel.style.background = 'linear-gradient(135deg, rgba(107, 44, 144, 0.08), rgba(245, 193, 44, 0.12))';
                    panel.style.backgroundSize = '';
                    infoPanel.style.background = 'rgba(255,255,255,0.92)';
                    break;
                case 'notebook':
                    panel.style.background = 'linear-gradient(#ffffff 25%, rgba(60, 105, 231, 0.08) 26%)';
                    panel.style.backgroundSize = '100% 22px';
                    infoPanel.style.background = 'rgba(255,255,255,0.95)';
                    break;
                case 'word-cloud':
                    panel.style.background = 'radial-gradient(circle at top left, rgba(60, 105, 231, 0.12), transparent 42%), radial-gradient(circle at bottom right, rgba(107, 44, 144, 0.15), transparent 45%)';
                    panel.style.backgroundSize = '';
                    infoPanel.style.background = 'rgba(255,255,255,0.95)';
                    break;
                default:
                    panel.style.background = 'rgba(255,255,255,0.95)';
                    panel.style.backgroundSize = '';
                    infoPanel.style.background = 'rgba(255,255,255,0.92)';
            }
        }

        function applyEffect(stageEffect, stage) {
            const resultCard = document.getElementById('resultCard');
            const animationClasses = Object.values(animationClassMap);
            resultCard.classList.remove(...animationClasses);

            const bgSuccess = 'linear-gradient(135deg, rgba(107, 44, 144, 0.04), rgba(46, 204, 113, 0.14))';
            const bgFail = 'linear-gradient(135deg, rgba(255, 206, 214, 0.18), rgba(255, 240, 240, 0.96))';

            if (stage === 'success') {
                resultCard.style.background = bgSuccess;
                resultCard.style.borderColor = 'rgba(46, 204, 113, 0.45)';
            } else {
                resultCard.style.background = bgFail;
                resultCard.style.borderColor = 'rgba(231, 76, 60, 0.35)';
            }

            if (!stageEffect) {
                return;
            }

            const animationKey = (stageEffect.animation || '').toLowerCase();
            const className = animationClassMap[animationKey];
            if (className) {
                resultCard.classList.add(className);
            }

            if (stage === 'success') {
                triggerCelebration();
            }
        }

        function triggerCelebration() {
            const overlay = document.createElement('div');
            overlay.className = 'celebration-overlay';
            document.body.appendChild(overlay);

            const colors = ['#6b2c90', '#f5c12c', '#f89b1c', '#3c69e7', '#ff7bb7'];
            const starEmojis = ['✨', '🌟', '💫', '🎉'];

            const confettiCount = 32;
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti-piece';
                confetti.style.left = `${Math.random() * 100}%`;
                confetti.style.top = `${Math.random() * 30 - 10}%`;
                confetti.style.backgroundColor = colors[i % colors.length];
                confetti.style.animationDelay = `${Math.random() * 0.6}s`;
                overlay.appendChild(confetti);
            }

            const burstCount = 8;
            for (let i = 0; i < burstCount; i++) {
                const star = document.createElement('div');
                star.className = 'star-burst';
                star.textContent = starEmojis[i % starEmojis.length];
                star.style.left = `${10 + i * 10}%`;
                star.style.top = `${20 + Math.random() * 20}%`;
                star.style.animationDelay = `${0.1 * i}s`;
                overlay.appendChild(star);
            }

            setTimeout(() => {
                overlay.remove();
            }, 3100);
        }

        function renderHighlights(data) {
            const meta = data.evaluation_meta || {};
            const segments = meta.highlight_segments || [];

            if (!segments.length) {
                return '';
            }

            return segments.map(segment => {
                const status = segment.status || 'correct';
                let className = 'hl-correct';
                if (status === 'wrong') className = 'hl-wrong';
                else if (status === 'neutral') className = 'hl-neutral';

                const title = segment.message ? ` title="${escapeHtml(segment.message)}"` : '';
                const text = segment.text || '';
                const safeText = escapeHtml(text).replace(/ /g, '&nbsp;');

                return `<span class="${className}"${title}>${safeText}</span>`;
            }).join('');
        }

        function renderNotes(data) {
            const meta = data.evaluation_meta || {};
            const notes = meta.notes || [];

            if (!notes.length) {
                return '';
            }

            const items = notes.map(note => `<li>${escapeHtml(note)}</li>`).join('');
            return `<ul class="hl-notes">${items}</ul>`;
        }

        function escapeHtml(str) {
            return (str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
        
        // Show error
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('error').style.display = 'block';
        }
        
        // Load question when page loads
        window.onload = loadQuestion;
    </script>
</body>
</html>
