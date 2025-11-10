// Writing Question Page JavaScript
let currentQuestionId = null;
let currentExerciseId = null;
let allQuestionsList = []; // Store all questions for navigation
let currentQuestionIndex = 0;
let questionEffects = {};
let exerciseEffects = {};
let currentExerciseTypeCode = null;
let currentStarterText = '';

const Effects = window.WritingEffects || {};
const Feedback = window.WritingFeedback || {};
const escapeHtml = (str) => {
    if (Feedback.escapeHtml) {
        return Feedback.escapeHtml(str);
    }
    return (str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

// Load question data
async function loadQuestion() {
    try {
        const pathParts = window.location.pathname.split('/').filter(part => part);
        const lastPart = pathParts[pathParts.length - 1];
        
        // Validate ID is a number
        if (!lastPart || isNaN(lastPart)) {
            throw new Error('Invalid question ID: ' + lastPart);
        }
        
        currentQuestionId = parseInt(lastPart);
        
        if (!currentQuestionId || currentQuestionId <= 0) {
            throw new Error('Question ID must be a positive number');
        }
        
        const response = await fetch(`/api/questions/${currentQuestionId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.status === 'success' && data.data) {
            const question = data.data;
            const exercise = question.exercise || {};
            const exerciseType = exercise.type || {};
            const lesson = exercise.lesson || {};
            const lessonTitle = lesson.title || 'I-CLC Lesson';
            currentExerciseTypeCode = exerciseType.code || null;
            currentStarterText = question.starter_text || '';
            
            // Update currentQuestionId and currentExerciseId to match loaded question
            currentQuestionId = question.id;
            currentExerciseId = exercise.id;

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

            const answerField = document.getElementById('userAnswer');
            const wcsGroup = document.getElementById('wcsInputGroup');
            const wcsPrefixEl = document.getElementById('wcsPrefix');
            const answerSuffixEl = document.getElementById('answerSuffix');

            const isWcs = currentExerciseTypeCode === 'WCS' && currentStarterText;

            if (isWcs && wcsGroup && wcsPrefixEl && answerSuffixEl) {
                wcsGroup.style.display = 'flex';
                const prefixText = currentStarterText.trim();
                wcsPrefixEl.textContent = prefixText;
                wcsPrefixEl.setAttribute('aria-label', 'Gợi ý cố định');
                answerSuffixEl.value = '';
                answerSuffixEl.placeholder = '...';
                answerSuffixEl.focus();

                if (answerField) {
                    answerField.style.display = 'none';
                    answerField.value = prefixText ? `${prefixText} ` : '';
                }
            } else {
                if (wcsGroup) {
                    wcsGroup.style.display = 'none';
                }
                if (answerField) {
                    answerField.style.display = 'block';
                    answerField.value = '';
                    answerField.placeholder = currentStarterText
                        ? `${currentStarterText.trim()} ...`
                        : 'Viết câu trả lời của con tại đây...';
                    answerField.focus();
                }
            }

            // Load all questions for navigation
            if (exercise.id) {
                await loadAllQuestions(exercise.id, question.id);
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
                const hint = buildHintMessage(currentExerciseTypeCode, question);
                if (hint) {
                    hintText.textContent = hint;
                    hintSection.style.display = 'block';
                } else {
                    hintSection.style.display = 'none';
                }
            }

            questionEffects = question.effect || {};
            exerciseEffects = questionEffects.exercise || {};
            applyExerciseBackground();
            loadTemplateHint(currentQuestionId);
        } else {
            const errorMsg = data.message || 'Failed to load question';
            throw new Error(errorMsg);
        }
    } catch (error) {
        console.error('Error loading question:', error);
        const errorMessage = error.message || 'Không thể tải câu hỏi. Vui lòng thử lại.';
        showError('Không thể tải câu hỏi: ' + errorMessage);
    }
}

// Submit answer
async function submitAnswer() {
    let answer = '';
    const answerField = document.getElementById('userAnswer');
    const answerSuffixEl = document.getElementById('answerSuffix');

    if (currentExerciseTypeCode === 'WCS') {
        if (answerSuffixEl) {
            const suffix = answerSuffixEl.value.trim();
            if (!suffix) {
                showError('Con hãy hoàn thành câu trước khi gửi nhé!');
                return;
            }
            const prefix = currentStarterText ? currentStarterText.trim() : '';
            answer = prefix ? `${prefix}${prefix.endsWith(' ') ? '' : ' '}${suffix}` : suffix;
            if (answerField) {
                answerField.value = answer;
            }
        } else if (answerField) {
            // Fallback nếu ô suffix không tồn tại (ví dụ trên trang chưa được cập nhật)
            answer = answerField.value.trim();
            if (!answer) {
                showError('Con hãy hoàn thành câu trước khi gửi nhé!');
                return;
            }
        } else {
            showError('Không tìm thấy ô trả lời.');
            return;
        }
    } else {
        if (!answerField) {
            showError('Không tìm thấy ô trả lời.');
            return;
        }
        answer = answerField.value.trim();
        if (!answer) {
            showError('Vui lòng viết câu trả lời trước khi gửi.');
            return;
        }
    }

    if (!currentQuestionId) {
        showError('ID câu hỏi không tìm thấy.');
        return;
    }

    if (Effects.unlockAudio) Effects.unlockAudio();
    if (Effects.playSound) {
        Effects.playSound(exerciseEffects.button_sound, 'button');
    }

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
    const exerciseTypeCode = question.exercise && question.exercise.type ? question.exercise.type.code : null;

    if (exerciseTypeCode === 'WCS' && question.starter_text) {
        return `Hoàn thành câu bắt đầu bằng "${question.starter_text.trim()}" nhé!`;
    }

    if (exerciseTypeCode === 'WAQ') {
        return 'Trả lời theo ý của con, nhớ viết thành câu đầy đủ nhé!';
    }

    if (exerciseTypeCode === 'WSG') {
        const keyword = question.prompt_text || question.target_text || '';
        if (keyword) {
            return `Đặt một câu hoàn chỉnh có từ "${keyword}" nhé!`;
        }
        return 'Đặt một câu hoàn chỉnh bằng cách dùng từ được cho nhé!';
    }

    if (question.starter_text) {
        return `Bé hãy viết tiếp câu bắt đầu bằng "${question.starter_text.trim()}" nhé!`;
    }

    return 'Viết câu trả lời đầy đủ, nhớ viết hoa chữ cái đầu và kết thúc bằng dấu chấm nhé!';
}

function buildHintMessage(exerciseTypeCode, question) {
    if (exerciseTypeCode === 'WCS' && question.starter_text) {
        return `Hoàn thành câu: "${question.starter_text.trim()} ..."`;
    }

    if (exerciseTypeCode === 'WAQ') {
        return 'Con trả lời theo ý của mình, miễn là đúng chủ đề và viết thành câu hoàn chỉnh nhé!';
    }

    if (exerciseTypeCode === 'WSG') {
        const keyword = question.prompt_text || question.target_text || '';
        if (keyword) {
            return `Dùng từ "${keyword}" để đặt một câu đầy đủ nhé con!`;
        }
        return 'Dùng từ được cho để đặt một câu hoàn chỉnh nhé con!';
    }

    if (question.starter_text) {
        return `Bắt đầu với: "${question.starter_text.trim()}" và hoàn thành câu nhé!`;
    }

    return 'Viết câu trả lời đầy đủ, nhớ viết hoa chữ cái đầu và kết thúc bằng dấu chấm nhé!';
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
            hintBox.textContent = '💡 Viết câu trả lời đầy đủ và đúng chủ đề nhé con!';
        }
    } catch (error) {
        hintBox.textContent = '💡 Không lấy được gợi ý, con hãy thử dựa vào đề bài nhé!';
    }
}

function resetAnswer() {
    const answerField = document.getElementById('userAnswer');
    const answerSuffixEl = document.getElementById('answerSuffix');

    if (currentExerciseTypeCode === 'WCS') {
        if (answerSuffixEl) {
            answerSuffixEl.value = '';
            answerSuffixEl.placeholder = '...';
            answerSuffixEl.focus();
        }
        if (answerField) {
            answerField.value = currentStarterText ? `${currentStarterText.trim()} ` : '';
        }
    } else if (answerField) {
        answerField.value = '';
        answerField.placeholder = currentStarterText
            ? `${currentStarterText.trim()} ...`
            : 'Viết câu trả lời của con tại đây...';
        answerField.focus();
    }

    document.getElementById('error').style.display = 'none';
    document.getElementById('resultCard').style.display = 'none';
}

function showResult(data) {
    const resultCard = document.getElementById('resultCard');
    const resultFeedback = document.getElementById('resultFeedback');
    const effectData = data.effect || {};
    const stage = data.is_correct ? 'success' : 'failure';
    let stageEffect = Effects.getStageEffect ? Effects.getStageEffect(effectData, stage) : null;
    if (!stageEffect && effectData && effectData[stage]) {
        stageEffect = effectData[stage];
    }
    if (!stageEffect && questionEffects && questionEffects.effects && questionEffects.effects[stage]) {
        stageEffect = questionEffects.effects[stage];
    }
    const effectMessage = stageEffect && stageEffect.message ? stageEffect.message : '';

    const statusText = data.is_correct ? 'Làm tốt lắm!' : 'Cùng thử lại nhé';
    const statusClass = data.is_correct ? 'success' : 'failure';
    const score = data.score ? `${data.score}/100` : (data.feedback.includes('Điểm:') ? data.feedback.split('Điểm:')[1].split('/')[0] + '/100' : 'N/A');

    const highlightHtml = Feedback.renderHighlights ? Feedback.renderHighlights(data) : '';
    const notesHtml = Feedback.renderNotes ? Feedback.renderNotes(data) : '';
    // Format markdown feedback to kid-friendly HTML
    const feedbackText = Feedback.formatFeedbackForKids ? Feedback.formatFeedbackForKids(data.feedback) : (data.feedback || '');

    resultFeedback.innerHTML = `
        <div class="status-row">
            <span class="status-pill ${statusClass}">✨ ${statusText}</span>
            <span class="score-pill">Điểm: ${score}</span>
        </div>
        <div class="result-text">${feedbackText}</div>
        ${effectMessage ? `<div class="effect-message">${Feedback.formatFeedbackForKids ? Feedback.formatFeedbackForKids(effectMessage) : effectMessage}</div>` : ''}
        ${notesHtml ? `<div class="hl-container">${notesHtml}</div>` : ''}
        <div class="result-actions">
            <button class="btn-primary" type="button" onclick="resetAnswer()">Làm thêm lần nữa</button>
            <a href="/writing" class="btn-secondary" style="text-decoration:none;">Chọn bài khác</a>
        </div>
    `;

    resultCard.style.display = 'block';
    resultCard.scrollIntoView({ behavior: 'smooth' });
    if (Effects.applyEffect) Effects.applyEffect(stageEffect, stage);
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

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').style.display = 'block';
}

async function loadAllQuestions(exerciseId, currentId) {
    try {
        if (!exerciseId) {
            console.warn('No exercise ID provided');
            return;
        }
        
        const response = await fetch(`/api/questions?exercise_id=${exerciseId}`);
        const data = await response.json();
        
        if (data.status === 'success' && data.data && Array.isArray(data.data)) {
            // Sort by order_index
            allQuestionsList = data.data.sort((a, b) => (a.order_index || 0) - (b.order_index || 0));
            
            // Find current question index - use both string and number comparison
            currentQuestionIndex = allQuestionsList.findIndex(q => 
                q.id == currentId || parseInt(q.id) === parseInt(currentId)
            );
            
            if (currentQuestionIndex === -1) {
                // If current question not found, try to find by matching currentQuestionId
                currentQuestionIndex = allQuestionsList.findIndex(q => 
                    q.id == currentQuestionId || parseInt(q.id) === parseInt(currentQuestionId)
                );
            }
            
            if (currentQuestionIndex === -1) {
                currentQuestionIndex = 0;
            }
            
            // Update dropdown selector
            const select = document.getElementById('questionSelect');
            if (select && currentId) {
                select.value = currentId;
            }
            
            // Update exercise selector
            const exerciseSelect = document.getElementById('exerciseSelect');
            if (exerciseSelect && exerciseId) {
                exerciseSelect.value = exerciseId;
            }
            
            // Update navigation buttons
            updateNavigationButtons();
            updateQuestionCounter();
        } else {
            console.warn('Failed to load questions:', data);
            allQuestionsList = [];
        }
    } catch (error) {
        console.error('Could not load all questions for navigation:', error);
        allQuestionsList = [];
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevQuestionBtn');
    const nextBtn = document.getElementById('nextQuestionBtn');
    
    if (!prevBtn || !nextBtn) return;
    
    // Only disable if we're sure we can't navigate
    const canGoPrev = currentQuestionIndex > 0 && allQuestionsList.length > 0;
    const canGoNext = currentQuestionIndex < allQuestionsList.length - 1 && allQuestionsList.length > 0;
    
    prevBtn.disabled = !canGoPrev;
    prevBtn.style.opacity = canGoPrev ? '1' : '0.5';
    prevBtn.style.cursor = canGoPrev ? 'pointer' : 'not-allowed';
    
    nextBtn.disabled = !canGoNext;
    nextBtn.style.opacity = canGoNext ? '1' : '0.5';
    nextBtn.style.cursor = canGoNext ? 'pointer' : 'not-allowed';
}

function updateQuestionCounter() {
    const counter = document.getElementById('questionCounter');
    if (counter && allQuestionsList.length > 0) {
        counter.textContent = `(${currentQuestionIndex + 1}/${allQuestionsList.length})`;
    }
}

function navigateToPrevious() {
    // Reload questions list if empty
    if (allQuestionsList.length === 0 && currentExerciseId) {
        loadAllQuestions(currentExerciseId, currentQuestionId).then(() => {
            navigateToPrevious();
        });
        return;
    }
    
    if (currentQuestionIndex > 0 && allQuestionsList.length > 0) {
        const prevQuestion = allQuestionsList[currentQuestionIndex - 1];
        if (prevQuestion && prevQuestion.id) {
            navigateToQuestion(prevQuestion.id);
        }
    }
}

function navigateToNext() {
    // Reload questions list if empty
    if (allQuestionsList.length === 0 && currentExerciseId) {
        loadAllQuestions(currentExerciseId, currentQuestionId).then(() => {
            navigateToNext();
        });
        return;
    }
    
    if (currentQuestionIndex < allQuestionsList.length - 1 && allQuestionsList.length > 0) {
        const nextQuestion = allQuestionsList[currentQuestionIndex + 1];
        if (nextQuestion && nextQuestion.id) {
            navigateToQuestion(nextQuestion.id);
        }
    }
}

function navigateToExercise(exerciseId) {
    if (exerciseId) {
        // Load first question of the selected exercise
        fetch(`/api/exercises/${exerciseId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.data.questions && data.data.questions.length > 0) {
                    // Get first question ordered by order_index
                    const firstQuestion = data.data.questions.sort((a, b) => (a.order_index || 0) - (b.order_index || 0))[0];
                    navigateToQuestion(firstQuestion.id);
                }
            })
            .catch(error => {
                console.error('Error loading exercise:', error);
            });
    }
}

function navigateToQuestion(questionId) {
    if (questionId) {
        // Check if we're in iframe mode (multiple ways to detect)
        const isInIframe = window.frameElement || 
                          window.self !== window.top || 
                          window.location.pathname.includes('/embed/');
        
        if (isInIframe) {
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

function initQuestionNavigationElements() {
    const prevBtn = document.getElementById('prevQuestionBtn');
    const nextBtn = document.getElementById('nextQuestionBtn');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.querySelector('.btn-secondary');
    const questionSelect = document.getElementById('questionSelect');

    if (prevBtn) {
        prevBtn.addEventListener('click', navigateToPrevious);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', navigateToNext);
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', submitAnswer);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', resetAnswer);
    }

    if (questionSelect) {
        questionSelect.addEventListener('change', (e) => {
            navigateToQuestion(e.target.value);
        });
    }
}

function initWritingQuestionPage() {
    loadQuestion();
    initTranslationFeature();
    initQuestionNavigationElements();

    ['click', 'keydown', 'touchstart'].forEach(eventType => {
        document.addEventListener(eventType, Effects.unlockAudio, { once: true, passive: true });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWritingQuestion);
} else {
    initWritingQuestionPage();
}

