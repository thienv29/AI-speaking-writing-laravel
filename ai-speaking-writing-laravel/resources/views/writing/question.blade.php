@extends('layouts.app')

@section('meta')
<title>Writing Adventure | I-CLC</title>
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
</div>
@endsection

@push('scripts')
<script>
    let currentQuestionId = null;
    let questionEffects = {};
    let exerciseEffects = {};
    let audioContext = null;
    let audioUnlocked = false;
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
        unlockAudio(); // Unlock audio on user interaction
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
            return `Bé hãy viết tiếp câu bắt đầu bằng "${question.starter_text}…" nhé!`;
        }
        switch (template) {
            case 'name':
                return 'Giới thiệu tên của con bằng câu "My name is …".';
            case 'age':
                return 'Nói tuổi của con bằng câu "I am [number] years old."';
            case 'location':
                return 'Hãy kể con đang sống ở đâu bằng câu "I live in …".';
            case 'time':
                return 'Dùng câu "It is [number] o\'clock." để nói giờ hiện tại nhé.';
            case 'weather':
                return 'Mô tả thời tiết bằng câu "Today is …".';
            case 'hobby':
                return 'Chia sẻ sở thích của con bằng câu "My favorite hobby is …".';
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
        
        // Debug: log effect data
        console.log('Effect data:', effectData);
        console.log('Stage:', stage);
        console.log('Stage effect:', stageEffect);
        console.log('Question effects:', questionEffects);
        
        // Play sound effect - ALWAYS play fallback tone if no sound file
        // Even if stageEffect exists, we'll try fallback tone as backup
        if (stageEffect && stageEffect.sound) {
            console.log('Playing effect sound:', stageEffect.sound);
            playSound(stageEffect.sound, stage);
        } else {
            // No stageEffect sound, use generic fallback
            console.log('No stageEffect sound, using fallback tone directly');
            playFallbackTone(stage);
        }
    }
    function getStageEffect(effectData, stage) {
        console.log('getStageEffect called with:', { effectData, stage });
        
        // Effect data structure: { success: {...}, failure: {...} }
        if (effectData && effectData[stage]) {
            console.log('Found effect in effectData:', effectData[stage]);
            return effectData[stage];
        }
        
        // Fallback: check questionEffects (loaded from question)
        if (questionEffects && questionEffects.effects && questionEffects.effects[stage]) {
            console.log('Found effect in questionEffects:', questionEffects.effects[stage]);
            return questionEffects.effects[stage];
        }
        
        console.log('No effect found, returning null');
        return null;
    }

    function playSound(url, stage) {
        if (!url) {
            console.log('No sound URL provided, using fallback tone');
            playFallbackTone(stage);
            return;
        }
        
        console.log('Attempting to play sound:', url);
        
        // Unlock audio first
        unlockAudio();
        
        try {
            let audio = audioCache[url];
            if (!audio) {
                audio = new Audio(url);
                audioCache[url] = audio;
                
                // Handle load error
                audio.addEventListener('error', (e) => {
                    console.warn('Failed to load sound:', url, e);
                    playFallbackTone(stage);
                });
                
                // Preload audio
                audio.load();
            }
            
            // Reset and play
            audio.currentTime = 0;
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        console.log('Sound played successfully:', url);
                    })
                    .catch(error => {
                        console.warn('Sound play failed (autoplay policy?):', url, error);
                        // Always fallback to tone since user interaction happened
                        playFallbackTone(stage);
                    });
            } else {
                // Fallback if play() doesn't return promise
                playFallbackTone(stage);
            }
        } catch (error) {
            console.warn('Unable to play sound:', error);
            playFallbackTone(stage);
        }
    }

    function ensureAudioContext() {
        if (audioContext) return audioContext;
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return null;
        audioContext = new AudioCtx();
        
        // Resume audio context if suspended (browser autoplay policy)
        if (audioContext.state === 'suspended') {
            audioContext.resume().then(() => {
                console.log('AudioContext resumed');
            }).catch(err => {
                console.warn('Failed to resume AudioContext:', err);
            });
        }
        
        return audioContext;
    }
    
    // Unlock audio on first user interaction
    function unlockAudio() {
        if (audioUnlocked) {
            // Still try to resume if suspended
            const ctx = ensureAudioContext();
            if (ctx && ctx.state === 'suspended') {
                ctx.resume().catch(() => {});
            }
            return;
        }
        
        const ctx = ensureAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume().then(() => {
                audioUnlocked = true;
                console.log('Audio unlocked successfully');
            }).catch(err => {
                console.warn('Failed to unlock audio:', err);
            });
        } else if (ctx) {
            audioUnlocked = true;
        }
        
        // Also try creating a silent audio to unlock HTML5 audio
        try {
            const silentAudio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA=');
            silentAudio.volume = 0.01;
            silentAudio.play().then(() => {
                console.log('HTML5 audio unlocked');
                audioUnlocked = true;
            }).catch(() => {});
        } catch (e) {}
    }

    function playFallbackTone(stage) {
        console.log('Playing fallback tone for stage:', stage);
        
        // Unlock audio first
        unlockAudio();
        
        // Wait a bit for audio to unlock, then try multiple times if needed
        let attempts = 0;
        const maxAttempts = 3;
        
        const tryPlayTone = () => {
            attempts++;
            const ctx = ensureAudioContext();
            if (!ctx) {
                console.warn('AudioContext not available');
                return;
            }

            // Ensure context is running
            if (ctx.state === 'suspended') {
                ctx.resume().then(() => {
                    console.log('AudioContext resumed, playing tone');
                    playTone(ctx, stage);
                }).catch(err => {
                    console.warn('Failed to resume AudioContext:', err);
                    // Retry after a delay if attempts < max
                    if (attempts < maxAttempts) {
                        setTimeout(tryPlayTone, 100 * attempts);
                    } else {
                        // Last attempt - try anyway
                        playTone(ctx, stage);
                    }
                });
            } else {
                playTone(ctx, stage);
            }
        };
        
        // Start with initial delay
        setTimeout(tryPlayTone, 50);
    }
    
    function playTone(ctx, stage) {
        const freq = fallbackToneMap[stage] || 440;
        const duration = stage === 'failure' ? 0.35 : 0.18;

        try {
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
            
            console.log('Fallback tone played:', freq, 'Hz');
        } catch (error) {
            console.error('Error playing fallback tone:', error);
        }
    }

    function applyExerciseBackground() {
        const panel = document.querySelector('.question-panel');
        const infoPanel = document.querySelector('.info-panel');
        
        // Đảm bảo background mặc định luôn được set
        if (!panel || !infoPanel) return;
        
        if (!exerciseEffects || !exerciseEffects.background) {
            // Sử dụng CSS class thay vì inline style để tránh override
            panel.style.background = '';
            panel.style.backgroundSize = '';
            infoPanel.style.background = '';
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
                // Reset về CSS mặc định
                panel.style.background = '';
                panel.style.backgroundSize = '';
                infoPanel.style.background = '';
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
    window.addEventListener('DOMContentLoaded', () => {
        loadQuestion();
        
        // Unlock audio on any user interaction
        ['click', 'keydown', 'touchstart'].forEach(eventType => {
            document.addEventListener(eventType, unlockAudio, { once: true, passive: true });
        });
    });
    
    // ========== Translation Feature: Bôi đen từ để dịch ==========
    let translationPopup = null;
    let translationTimeout = null;
    
    function initTranslationFeature() {
        // Tạo translation popup
        translationPopup = document.createElement('div');
        translationPopup.id = 'translation-popup';
        translationPopup.className = 'translation-popup';
        document.body.appendChild(translationPopup);
        
        // Detect text selection
        document.addEventListener('mouseup', handleTextSelection);
        document.addEventListener('touchend', handleTextSelection);
        
        // Hide popup when clicking outside
        document.addEventListener('click', function(e) {
            if (!translationPopup.contains(e.target)) {
                hideTranslationPopup();
            }
        });
    }
    
    function handleTextSelection(e) {
        const selection = window.getSelection();
        const selectedText = selection.toString().trim();
        
        // Chỉ translate nếu có text được chọn và không phải là trong input/textarea
        if (selectedText.length === 0) {
            hideTranslationPopup();
            return;
        }
        
        const activeElement = document.activeElement;
        if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA')) {
            hideTranslationPopup();
            return;
        }
        
        // Chỉ translate text tiếng Anh (không chứa ký tự tiếng Việt)
        if (!/^[a-zA-Z\s'-]+$/.test(selectedText)) {
            hideTranslationPopup();
            return;
        }
        
        // Debounce
        clearTimeout(translationTimeout);
        translationTimeout = setTimeout(() => {
            translateText(selectedText, e);
        }, 300);
    }
    
    function translateText(text, event) {
        if (!text || text.length === 0) return;
        
        // Show loading
        showTranslationPopup('Đang dịch...', event);
        
        // Call API (API routes don't need CSRF token)
        fetch('/api/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ text: text })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'API Error');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.translation) {
                showTranslationPopup(data.translation, event, text);
            } else {
                showTranslationPopup('Không tìm thấy bản dịch', event);
            }
        })
        .catch(error => {
            showTranslationPopup('Lỗi khi dịch: ' + error.message, event);
        });
    }
    
    function showTranslationPopup(translation, event, originalText = '') {
        if (!translationPopup) return;
        
        translationPopup.innerHTML = `
            ${originalText ? `<div class="translation-original">${escapeHtml(originalText)}</div>` : ''}
            <div class="translation-text">${escapeHtml(translation)}</div>
        `;
        translationPopup.style.display = 'block';
        
        // Position popup near cursor
        const x = event?.clientX || window.innerWidth / 2;
        const y = event?.clientY || window.innerHeight / 2;
        
        translationPopup.style.left = Math.min(x + 20, window.innerWidth - 280) + 'px';
        translationPopup.style.top = Math.min(y + 20, window.innerHeight - 120) + 'px';
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            hideTranslationPopup();
        }, 5000);
    }
    
    function hideTranslationPopup() {
        if (translationPopup) {
            translationPopup.style.display = 'none';
        }
    }
    
    // Initialize translation feature when page loads
    window.addEventListener('DOMContentLoaded', initTranslationFeature);
</script>
@endpush
