// Writing Question Page JavaScript
let currentQuestionId = null;
let allQuestionsList = []; // Store all questions for navigation
let currentQuestionIndex = 0;
let questionEffects = {};
let exerciseEffects = {};
let audioContext = null;
let audioUnlocked = false;
let audioCache = {};

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
        const pathParts = window.location.pathname.split('/');
        currentQuestionId = pathParts[pathParts.length - 1];
        
        const response = await fetch(`/api/questions/${currentQuestionId}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const question = data.data;
            const exercise = question.exercise || {};
            const exerciseType = exercise.type || {};
            const lesson = exercise.lesson || {};
            const lessonTitle = lesson.title || 'I-CLC Lesson';

            // Safe element updates - check if element exists before setting
            const safeSetText = (id, text) => {
                const el = document.getElementById(id);
                if (el) el.textContent = text;
            };
            
            safeSetText('exerciseTitleHeading', exercise.title || 'Writing Exercise');
            safeSetText('exerciseTitle', exercise.title || 'Writing Exercise');
            safeSetText('lessonPill', lessonTitle);
            safeSetText('lessonBadge', `Bài học: ${lessonTitle}`);
            safeSetText('infoLesson', lessonTitle);
            safeSetText('infoExercise', exerciseType.name || 'Writing Practice');
            safeSetText('typeTag', exerciseType.code || exerciseType.name || '—');
            safeSetText('templateTag', ((question.effect && question.effect.template) || 'general').toUpperCase());
            safeSetText('orderTag', `#${question.order_index ?? '?'}`);
            const promptText = question.prompt_text || (question.starter_text ? `${question.starter_text} ...` : '—');
            safeSetText('questionPrompt', promptText);
            safeSetText('exerciseSubtitle', buildSubtitle(question));

            // Load all questions for navigation if not already loaded
            if (exercise.id) {
                // Reset when loading new question
                if (allQuestionsList.length === 0 || currentQuestionId != question.id) {
                    allQuestionsList = [];
                    loadAllQuestions(exercise.id, question.id);
                }
            }

            // Display exercise instruction
            const instructionSection = document.getElementById('instructionSection');
            const instructionText = document.getElementById('instructionText');
            if (instructionSection && instructionText && exercise.instruction) {
                instructionText.textContent = exercise.instruction;
                instructionSection.style.display = 'block';
            }

            // Display template hint
            const hintSection = document.getElementById('hintSection');
            const hintText = document.getElementById('hintText');
            if (hintSection && hintText) {
                const template = (question.effect && question.effect.template) || '';
                const hint = getTemplateHint(template, question);
                if (hint) {
                    hintText.textContent = hint;
                    hintSection.style.display = 'block';
                }
            }

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
    
    unlockAudio();
    playSound(exerciseEffects.button_sound, 'button');

    const submitBtn = document.getElementById('submitBtn');
    const loadingEl = document.getElementById('loading');
    const errorEl = document.getElementById('error');
    const resultCard = document.getElementById('resultCard');
    
    submitBtn.disabled = true;
    loadingEl.style.display = 'flex';
    errorEl.style.display = 'none';
    resultCard.style.display = 'none';
    
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
        submitBtn.disabled = false;
        loadingEl.style.display = 'none';
    }
}

function buildSubtitle(question) {
    const template = (question.effect && question.effect.template) || '';
    if (question.starter_text) {
        return `Bé hãy viết tiếp câu bắt đầu bằng "${question.starter_text}…" nhé!`;
    }
    const subtitles = {
        name: 'Giới thiệu tên của con bằng câu "My name is …".',
        age: 'Nói tuổi của con bằng câu "I am [number] years old."',
        location: 'Hãy kể con đang sống ở đâu bằng câu "I live in …".',
        time: 'Dùng câu "It is [number] o\'clock." để nói giờ hiện tại nhé.',
        weather: 'Mô tả thời tiết bằng câu "Today is …".',
        hobby: 'Chia sẻ sở thích của con bằng câu "My favorite hobby is …".'
    };
    return subtitles[template] || 'Viết câu trả lời đầy đủ, nhớ viết hoa chữ cái đầu và kết thúc bằng dấu chấm nhé!';
}

function getTemplateHint(template, question) {
    if (question.starter_text) {
        return `Viết tiếp câu: "${question.starter_text} [phần còn lại của câu]"`;
    }
    
    const hints = {
        name: 'Format: Hello, [tên của bạn] hoặc My name is [tên của bạn].',
        greeting: 'Format: Hello, [tên của bạn].',
        age: 'Format: I am [số tuổi] years old.',
        location: 'Format: I live in [nơi bạn sống].',
        time: 'Format: It is [số giờ] o\'clock (chỉ dùng với giờ tròn, không dùng phút).',
        weather: 'Format: Today is [sunny/rainy/cloudy/windy/snowy].',
        hobby: 'Format: My favorite hobby is [sở thích của bạn].'
    };
    
    return hints[template] || null;
}

async function loadTemplateHint(questionId) {
    const hintBox = document.getElementById('infoHint');
    if (!hintBox) return; // Skip if element doesn't exist (e.g., in embed mode)
    
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
    // Format markdown feedback to kid-friendly HTML
    const feedbackText = formatFeedbackForKids(data.feedback);

    resultFeedback.innerHTML = `
        <div class="status-row">
            <span class="status-pill ${statusClass}">✨ ${statusText}</span>
            <span class="score-pill">Điểm: ${score}</span>
        </div>
        <div class="result-text">${feedbackText}</div>
        ${effectMessage ? `<div class="effect-message">${formatFeedbackForKids(effectMessage)}</div>` : ''}
        ${notesHtml ? `<div class="hl-container">${notesHtml}</div>` : ''}
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
    } else {
        playFallbackTone(stage);
    }
}

function getStageEffect(effectData, stage) {
    if (effectData && effectData[stage]) {
        return effectData[stage];
    }
    if (questionEffects && questionEffects.effects && questionEffects.effects[stage]) {
        return questionEffects.effects[stage];
    }
    return null;
}

function playSound(url, stage) {
    if (!url) {
        playFallbackTone(stage);
        return;
    }
    
    unlockAudio();
    
    try {
        let audio = audioCache[url];
        if (!audio) {
            audio = new Audio(url);
            audioCache[url] = audio;
            audio.addEventListener('error', () => playFallbackTone(stage));
            audio.load();
        }
        
        audio.currentTime = 0;
        const playPromise = audio.play();
        
        if (playPromise !== undefined) {
            playPromise.catch(() => playFallbackTone(stage));
        } else {
            playFallbackTone(stage);
        }
    } catch (error) {
        playFallbackTone(stage);
    }
}

function ensureAudioContext() {
    if (audioContext) return audioContext;
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return null;
    audioContext = new AudioCtx();
    
    if (audioContext.state === 'suspended') {
        audioContext.resume().catch(() => {});
    }
    
    return audioContext;
}

function unlockAudio() {
    if (audioUnlocked) {
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
        }).catch(() => {});
    } else if (ctx) {
        audioUnlocked = true;
    }
    
    try {
        const silentAudio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA=');
        silentAudio.volume = 0.01;
        silentAudio.play().then(() => {
            audioUnlocked = true;
        }).catch(() => {});
    } catch (e) {}
}

function playFallbackTone(stage) {
    unlockAudio();
    
    let attempts = 0;
    const maxAttempts = 3;
    
    const tryPlayTone = () => {
        attempts++;
        const ctx = ensureAudioContext();
        if (!ctx) return;

        if (ctx.state === 'suspended') {
            ctx.resume().then(() => {
                playTone(ctx, stage);
            }).catch(() => {
                if (attempts < maxAttempts) {
                    setTimeout(tryPlayTone, 100 * attempts);
                } else {
                    playTone(ctx, stage);
                }
            });
        } else {
            playTone(ctx, stage);
        }
    };
    
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
    } catch (error) {
        console.error('Error playing fallback tone:', error);
    }
}

function applyExerciseBackground() {
    const panel = document.querySelector('.question-panel');
    const infoPanel = document.querySelector('.info-panel');
    
    if (!panel || !infoPanel) return;
    
    if (!exerciseEffects || !exerciseEffects.background) {
        panel.style.background = '';
        panel.style.backgroundSize = '';
        infoPanel.style.background = '';
        return;
    }

    const backgrounds = {
        stars: {
            panel: 'linear-gradient(135deg, rgba(107, 44, 144, 0.08), rgba(245, 193, 44, 0.12))',
            info: 'rgba(255,255,255,0.92)'
        },
        notebook: {
            panel: 'linear-gradient(#ffffff 25%, rgba(60, 105, 231, 0.08) 26%)',
            panelSize: '100% 22px',
            info: 'rgba(255,255,255,0.95)'
        },
        'word-cloud': {
            panel: 'radial-gradient(circle at top left, rgba(60, 105, 231, 0.12), transparent 42%), radial-gradient(circle at bottom right, rgba(107, 44, 144, 0.15), transparent 45%)',
            info: 'rgba(255,255,255,0.95)'
        }
    };

    const bg = backgrounds[exerciseEffects.background];
    if (bg) {
        panel.style.background = bg.panel;
        panel.style.backgroundSize = bg.panelSize || '';
        infoPanel.style.background = bg.info;
    } else {
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

    if (!stageEffect) return;

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

    for (let i = 0; i < 32; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti-piece';
        confetti.style.left = `${Math.random() * 100}%`;
        confetti.style.top = `${Math.random() * 30 - 10}%`;
        confetti.style.backgroundColor = colors[i % colors.length];
        confetti.style.animationDelay = `${Math.random() * 0.6}s`;
        overlay.appendChild(confetti);
    }

    for (let i = 0; i < 8; i++) {
        const star = document.createElement('div');
        star.className = 'star-burst';
        star.textContent = starEmojis[i % starEmojis.length];
        star.style.left = `${10 + i * 10}%`;
        star.style.top = `${20 + Math.random() * 20}%`;
        star.style.animationDelay = `${0.1 * i}s`;
        overlay.appendChild(star);
    }

    setTimeout(() => overlay.remove(), 3100);
}

function renderHighlights(data) {
    const meta = data.evaluation_meta || {};
    const segments = meta.highlight_segments || [];
    if (!segments.length) return '';

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
    if (!notes.length) return '';
    return `<ul class="hl-notes">${notes.map(note => `<li>${escapeHtml(note)}</li>`).join('')}</ul>`;
}

function escapeHtml(str) {
    return (str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Format markdown feedback to simple, kid-friendly HTML
 * Converts markdown syntax to readable format for children
 */
function formatFeedbackForKids(markdownText) {
    if (!markdownText) return '';
    
    let text = markdownText;
    
    // Escape HTML first to prevent XSS
    text = escapeHtml(text);
    
    // Convert markdown headings to simple bold text
    text = text.replace(/^### (.*$)/gim, '<p class="feedback-heading"><strong>$1</strong></p>');
    text = text.replace(/^## (.*$)/gim, '<p class="feedback-heading"><strong>$1</strong></p>');
    text = text.replace(/^# (.*$)/gim, '<p class="feedback-heading"><strong>$1</strong></p>');
    
    // Convert bold (**text** or __text__) to <strong>
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/__(.+?)__/g, '<strong>$1</strong>');
    
    // Convert italic (*text* or _text_) to <em>
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    text = text.replace(/_(.+?)_/g, '<em>$1</em>');
    
    // Convert unordered lists (-, *, +) to simple bullet points
    text = text.replace(/^[\s]*[-*+]\s+(.+)$/gim, '<p class="feedback-list-item">• $1</p>');
    
    // Convert numbered lists (1. 2. 3.) to simple numbered items
    text = text.replace(/^[\s]*\d+\.\s+(.+)$/gim, '<p class="feedback-list-item">$1</p>');
    
    // Convert line breaks (\n\n) to paragraph breaks
    text = text.split(/\n\s*\n/).map(para => {
        para = para.trim();
        if (!para) return '';
        // If not already wrapped in a tag, wrap in <p>
        if (!para.match(/^<[a-z]/i)) {
            return '<p class="feedback-paragraph">' + para + '</p>';
        }
        return para;
    }).join('');
    
    // Convert single line breaks to <br>
    text = text.replace(/\n/g, '<br>');
    
    // Remove code blocks (```code```)
    text = text.replace(/```[\s\S]*?```/g, '');
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    
    // Clean up empty paragraphs
    text = text.replace(/<p[^>]*>\s*<\/p>/g, '');
    
    return text.trim();
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').style.display = 'block';
}

async function loadAllQuestions(exerciseId, currentId) {
    try {
        const response = await fetch(`/api/questions?exercise_id=${exerciseId}`);
        const data = await response.json();
        if (data.status === 'success' && data.data) {
            allQuestionsList = data.data.sort((a, b) => (a.order_index || 0) - (b.order_index || 0));
            // Find current question index
            currentQuestionIndex = allQuestionsList.findIndex(q => q.id == currentId);
            if (currentQuestionIndex === -1) currentQuestionIndex = 0;
            
            // Update dropdown selector
            const select = document.getElementById('questionSelect');
            if (select) {
                select.value = currentId;
            }
            
            // Update navigation buttons
            updateNavigationButtons();
            updateQuestionCounter();
        }
    } catch (error) {
        console.warn('Could not load all questions for navigation:', error);
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevQuestionBtn');
    const nextBtn = document.getElementById('nextQuestionBtn');
    
    if (prevBtn) {
        prevBtn.disabled = currentQuestionIndex <= 0;
        prevBtn.style.opacity = currentQuestionIndex <= 0 ? '0.5' : '1';
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentQuestionIndex >= allQuestionsList.length - 1;
        nextBtn.style.opacity = currentQuestionIndex >= allQuestionsList.length - 1 ? '0.5' : '1';
    }
}

function updateQuestionCounter() {
    const counter = document.getElementById('questionCounter');
    if (counter && allQuestionsList.length > 0) {
        counter.textContent = `(${currentQuestionIndex + 1}/${allQuestionsList.length})`;
    }
}

function navigateToPrevious() {
    if (currentQuestionIndex > 0 && allQuestionsList.length > 0) {
        const prevQuestion = allQuestionsList[currentQuestionIndex - 1];
        navigateToQuestion(prevQuestion.id);
    }
}

function navigateToNext() {
    if (currentQuestionIndex < allQuestionsList.length - 1 && allQuestionsList.length > 0) {
        const nextQuestion = allQuestionsList[currentQuestionIndex + 1];
        navigateToQuestion(nextQuestion.id);
    }
}

function navigateToQuestion(questionId) {
    if (questionId) {
        // Check if we're in iframe mode
        if (window.frameElement) {
            // In iframe, change URL but stay in embed
            window.location.href = `/embed/question/${questionId}`;
        } else {
            // Normal navigation
            window.location.href = `/writing/question/${questionId}`;
        }
    }
}

// Translation Feature
let translationPopup = null;
let translationTimeout = null;

function initTranslationFeature() {
    translationPopup = document.createElement('div');
    translationPopup.id = 'translation-popup';
    translationPopup.className = 'translation-popup';
    document.body.appendChild(translationPopup);
    
    document.addEventListener('mouseup', handleTextSelection);
    document.addEventListener('touchend', handleTextSelection);
    
    document.addEventListener('click', function(e) {
        if (!translationPopup.contains(e.target)) {
            hideTranslationPopup();
        }
    });
}

function handleTextSelection(e) {
    const selection = window.getSelection();
    const selectedText = selection.toString().trim();
    
    if (selectedText.length === 0) {
        hideTranslationPopup();
        return;
    }
    
    const activeElement = document.activeElement;
    if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA')) {
        hideTranslationPopup();
        return;
    }
    
    const cleanedText = selectedText.replace(/[.,!?;:]/g, '');
    if (!/^[a-zA-Z\s'-]+$/.test(cleanedText)) {
        hideTranslationPopup();
        return;
    }
    
    clearTimeout(translationTimeout);
    translationTimeout = setTimeout(() => {
        translateText(selectedText, e);
    }, 300);
}

function translateText(text, event) {
    if (!text || text.length === 0) return;
    
    showTranslationPopup('Đang dịch...', event);
    
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
    
    const x = event?.clientX || window.innerWidth / 2;
    const y = event?.clientY || window.innerHeight / 2;
    
    translationPopup.style.left = Math.min(x + 20, window.innerWidth - 280) + 'px';
    translationPopup.style.top = Math.min(y + 20, window.innerHeight - 120) + 'px';
    
    setTimeout(() => hideTranslationPopup(), 5000);
}

function hideTranslationPopup() {
    if (translationPopup) {
        translationPopup.style.display = 'none';
    }
}

// Initialize when page loads
window.addEventListener('DOMContentLoaded', () => {
    loadQuestion();
    initTranslationFeature();
    
    ['click', 'keydown', 'touchstart'].forEach(eventType => {
        document.addEventListener(eventType, unlockAudio, { once: true, passive: true });
    });
});

