//import './utils/translationBySelectText'
import { isEmpty } from 'lodash';
import confetti from 'canvas-confetti';
import questionApi from '../../api/questionApi';
import { initTranslationFeature } from './translationBySelectText';
import Feedback from './writing-feedback';

// Current question id
const question = window.appData.question;
const userId = "2";

const prevBtn = document.getElementById('prevQuestionBtn');
const nextBtn = document.getElementById('nextQuestionBtn');
const submitBtn = document.getElementById('submitBtn');
const resetBtn = document.getElementById('resetBtn');
const typeSelect = document.getElementById('typeSelect');
const lessonSelect = document.getElementById('lessonSelect');
const questionSelect = document.getElementById('questionSelect');
const exerciseSelect = document.getElementById('exerciseSelect');
const writingWrapper = document.getElementById('writingAnswerWrapper');
const speakingWrapper = document.getElementById('speakingAnswerWrapper');

const loadingEl = document.getElementById('loading');
const errorEl = document.getElementById('error');

const resultCard = document.getElementById('resultCard');
const answerField = document.getElementById('userAnswer');
const answerSuffixEl = document.getElementById('answerSuffix');
const resultFeedback = document.getElementById('resultFeedback');
const questionCounter = document.getElementById('questionCounter');
const questionSection = document.querySelector('.nav-section--question');
const exerciseTitleHeadingEl = document.getElementById('exerciseTitleHeading');
const exerciseTitleEl = document.getElementById('exerciseTitle');
const exerciseSubtitleEl = document.getElementById('instructionText');
const instructionSectionEl = document.getElementById('instructionSection');
const hintSectionEl = document.getElementById('hintSection');
const hintTextEl = document.getElementById('hintText');
const lessonPillEl = document.getElementById('lessonPill');
const typeTagEl = document.getElementById('typeTag');
const orderTagEl = document.getElementById('orderTag');
const questionPromptEl = document.getElementById('questionPrompt');
const wcsInputGroupEl = document.getElementById('wcsInputGroup');
const wcsPrefixEl = document.getElementById('wcsPrefix');
const speakingPromptEl = document.getElementById('question-prompt-text');
const speakingImageEl = document.getElementById('question-img');
const speakingPlaybackEl = document.getElementById('playback');
const navigationData = Array.isArray(window.appData.navigation) ? window.appData.navigation : [];
const currentContext = window.appData.current || {};

const exerciseQuestions = Array.isArray(question?.exercise?.questions) ? question.exercise.questions : [];
const totalQuestions = exerciseQuestions.length;
const currentQuestionIndex = Math.max(
    0,
    exerciseQuestions.findIndex((q) => q.id === question.id)
);

const recordBtn = document.getElementById('mic-btn');
const sampleAudioBtn = document.getElementById('sample-audio-btn');
const recordingIndicator = document.getElementById('recording-indicator');
const userAudio = document.getElementById('user-audio');

const SOUND_BASE_PATH = '/assets/sounds';
const SOUND_MAP = {
    button: `${SOUND_BASE_PATH}/mouseclick.mp3`,
    success: `${SOUND_BASE_PATH}/tryagain.mp3`,
    failure: `${SOUND_BASE_PATH}/fail.mp3`,
};

const audioCache = {};

let sampleAudio = null;

const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = null, userAnswer = '';
let mediaRecorder = null, mediaStream = null, chunks = [];
let mediaSupported = false;
let isRecording = false;

function triggerSuccessEffect(score = null) {
    try {
        const intensity = score && Number.isFinite(score) ? Math.min(Math.max(score, 50), 100) : 90;
        confetti({
            particleCount: Math.round(intensity),
            startVelocity: 35,
            spread: 65,
            origin: { y: 0.6 },
            scalar: 0.8,
        });
    } catch (error) {
        console.warn('[questionPage] Không thể chạy hiệu ứng confetti', error);
    }
}

function sanitizeFeedbackMessage(feedback, answer = '') {
    if (!feedback) return feedback;
    const trimmedAnswer = (answer || '').trim();
    if (!trimmedAnswer) return feedback;

    const endsWithPunctuation = /[.!?…]+$/.test(trimmedAnswer);
    if (!endsWithPunctuation) return feedback;

    return feedback.replace(/Lần sau con nhớ thêm dấu chấm cuối câu nhé\.*\s*/gi, '').trim();
}

function extractScoreFromFeedback(feedback) {
    if (!feedback) return null;
    const scoreMatch = feedback.match(/Điểm:\s*(\d+)\s*\/\s*100/i);
    if (scoreMatch && scoreMatch[1]) {
        const parsed = Number.parseInt(scoreMatch[1], 10);
        return Number.isNaN(parsed) ? null : parsed;
    }
    return null;
}

const toNumber = (value) => {
    if (value === null || value === undefined || value === '') return null;
    const num = Number(value);
    return Number.isNaN(num) ? null : num;
};

let activeTypeCode = currentContext.type || (question?.exercise?.type?.code ?? (navigationData[0]?.code ?? null));
let activeLessonId = toNumber(currentContext.lesson_id ?? question?.exercise?.lesson_id);
let activeExerciseId = toNumber(currentContext.exercise_id ?? question?.exercise?.id);

function renderTypeOptions() {
    if (!typeSelect) return;
    if (!navigationData.length) {
        typeSelect.innerHTML = '<option value="">Không có dạng bài</option>';
        typeSelect.disabled = true;
        return;
    }

    const optionsHtml = navigationData.map((type) => {
        const label = type.name || type.code;
        const selected = type.code === activeTypeCode ? 'selected' : '';
        return `<option value="${type.code}" ${selected}>${label}</option>`;
    }).join('');

    typeSelect.innerHTML = optionsHtml;
    typeSelect.disabled = navigationData.length <= 1;

    if (!navigationData.some((type) => type.code === activeTypeCode)) {
        activeTypeCode = navigationData[0].code;
        typeSelect.value = activeTypeCode;
    }
}

function updateLessonOptions(typeCode) {
    if (!lessonSelect) return;
    const typeData = navigationData.find((type) => type.code === typeCode);
    const lessons = Array.isArray(typeData?.lessons) ? typeData.lessons : [];

    if (!lessons.length) {
        lessonSelect.innerHTML = '<option value="">Không có bài học</option>';
        lessonSelect.disabled = true;
        activeLessonId = null;
        return;
    }

    const optionsHtml = lessons.map((lesson) => {
        const selected = Number(lesson.id) === Number(activeLessonId) ? 'selected' : '';
        return `<option value="${lesson.id}" ${selected}>${lesson.title}</option>`;
    }).join('');

    lessonSelect.innerHTML = optionsHtml;
    lessonSelect.disabled = lessons.length <= 1;

    if (!lessons.some((lesson) => Number(lesson.id) === Number(activeLessonId))) {
        activeLessonId = lessons[0].id;
        lessonSelect.value = String(activeLessonId);
    }
}

function getFirstQuestionIdFromExercise(exerciseItem) {
    if (!exerciseItem) return null;
    if (exerciseItem.first_question_id) return exerciseItem.first_question_id;
    if (Array.isArray(exerciseItem.questions) && exerciseItem.questions.length > 0) {
        return exerciseItem.questions[0].id ?? null;
    }
    return null;
}

function updateExerciseOptions(typeCode, lessonId) {
    if (!exerciseSelect) return null;
    const typeData = navigationData.find((type) => type.code === typeCode);
    const lessonData = typeData?.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
    const exercises = Array.isArray(lessonData?.exercises) ? lessonData.exercises : [];

    if (!exercises.length) {
        exerciseSelect.innerHTML = '<option value="">Không có bài tập</option>';
        exerciseSelect.disabled = true;
        activeExerciseId = null;
        return null;
    }

    const optionsHtml = exercises.map((exerciseItem) => {
        const selected = Number(exerciseItem.id) === Number(activeExerciseId) ? 'selected' : '';
        const questionId = getFirstQuestionIdFromExercise(exerciseItem);
        const value = questionId ?? '';
        return `<option value="${value}" data-exercise-id="${exerciseItem.id}" ${selected}>${exerciseItem.title}</option>`;
    }).join('');

    exerciseSelect.innerHTML = optionsHtml;
    exerciseSelect.disabled = exercises.length <= 1;

    if (!exercises.some((exerciseItem) => Number(exerciseItem.id) === Number(activeExerciseId))) {
        activeExerciseId = exercises[0].id;
    }

    const selectedExercise = exercises.find((exerciseItem) => Number(exerciseItem.id) === Number(activeExerciseId)) || exercises[0];
    const selectedQuestionId = getFirstQuestionIdFromExercise(selectedExercise);
    if (selectedQuestionId) {
        exerciseSelect.value = String(selectedQuestionId);
    }

    return selectedQuestionId;
}

function getFirstQuestionIdForType(typeCode) {
    const typeData = navigationData.find((type) => type.code === typeCode);
    if (!typeData || !Array.isArray(typeData.lessons)) return null;

    for (const lesson of typeData.lessons) {
        if (!Array.isArray(lesson.exercises)) continue;
        for (const exercise of lesson.exercises) {
            const questionId = getFirstQuestionIdFromExercise(exercise);
            if (questionId) {
                return Number(questionId);
            }
        }
    }

    return null;
}

function initNavigationSelectors() {
    if (!navigationData.length) return;

    if (!activeTypeCode) {
        activeTypeCode = navigationData[0]?.code ?? null;
    }

    renderTypeOptions();
    updateLessonOptions(activeTypeCode);
    updateExerciseOptions(activeTypeCode, activeLessonId);

    if (typeSelect) {
        typeSelect.addEventListener('change', (e) => {
            const selectedType = e.target.value;
            if (!selectedType || selectedType === activeTypeCode) return;

            activeTypeCode = selectedType;
            const typeData = navigationData.find((type) => type.code === activeTypeCode);
            const lessons = Array.isArray(typeData?.lessons) ? typeData.lessons : [];
            const firstLesson = lessons[0] ?? null;

            activeLessonId = firstLesson ? Number(firstLesson.id) : null;
            activeExerciseId = null;

            updateLessonOptions(activeTypeCode);
            const questionId = updateExerciseOptions(activeTypeCode, activeLessonId);

            if (questionId) {
                window.location.assign(`/embed/question/${questionId}`);
            } else {
                const fallbackId = getFirstQuestionIdForType(activeTypeCode);
                if (fallbackId) {
                    window.location.assign(`/embed/question/${fallbackId}`);
                }
            }
        });
    }

    if (lessonSelect) {
        lessonSelect.addEventListener('change', (e) => {
            const selectedLessonId = toNumber(e.target.value);
            if (!selectedLessonId || selectedLessonId === activeLessonId) return;

            activeLessonId = selectedLessonId;
            activeExerciseId = null;

            const questionId = updateExerciseOptions(activeTypeCode, activeLessonId);

            if (questionId) {
                window.location.assign(`/embed/question/${questionId}`);
            } else {
                const typeData = navigationData.find((type) => type.code === activeTypeCode);
                const lessonData = typeData?.lessons?.find((lesson) => Number(lesson.id) === Number(activeLessonId));
                const firstExercise = lessonData?.exercises?.[0];
                const fallbackId = getFirstQuestionIdFromExercise(firstExercise);
                if (fallbackId) {
                    window.location.assign(`/embed/question/${fallbackId}`);
                }
            }
        });
    }

    if (exerciseSelect) {
        exerciseSelect.addEventListener('change', (e) => {
            const option = e.target.selectedOptions && e.target.selectedOptions[0];
            if (!option) return;

            const questionId = option.value;
            const exerciseId = toNumber(option.dataset.exerciseId);
            if (exerciseId) {
                activeExerciseId = exerciseId;
            }

            if (questionId) {
                window.location.assign(`/embed/question/${questionId}`);
            }
        });
    }
}

function hydrateQuestionContent() {
    if (!question) return;

    const exercise = question.exercise || {};
    const exerciseType = exercise.type || {};
    const lesson = exercise.lesson || {};
    const rawTypeCode = (exerciseType.code || '').toString().toUpperCase();
    const isWritingType = rawTypeCode.startsWith('W');
    const isSpeakingType = rawTypeCode.startsWith('S');
    const isWcs = rawTypeCode === 'WCS';

    if (writingWrapper) {
        writingWrapper.style.display = isWritingType ? '' : 'none';
    }

    if (speakingWrapper) {
        speakingWrapper.style.display = isSpeakingType ? '' : 'none';
    }

    if (exerciseTitleHeadingEl) {
        exerciseTitleHeadingEl.textContent = exercise.title || (isSpeakingType ? 'Speaking Exercise' : 'Writing Exercise');
    }

    if (exerciseTitleEl) {
        exerciseTitleEl.textContent = `Câu ${question.order_index ?? ''}`;
    }

    if (typeTagEl) {
        typeTagEl.textContent = rawTypeCode || '—';
    }

    if (orderTagEl) {
        orderTagEl.textContent = `#${question.order_index ?? '—'}`;
    }

    if (lessonPillEl) {
        lessonPillEl.textContent = lesson.title || 'I-CLC';
    }

    if (questionPromptEl) {
        questionPromptEl.textContent = question.prompt_text || '—';
    }

    if (speakingPromptEl) {
        speakingPromptEl.textContent = question.prompt_text || '—';
    }

    if (speakingImageEl) {
        const fallbackSrc = speakingImageEl.getAttribute('data-default-src') || speakingImageEl.src;
        if (question.img_url) {
            speakingImageEl.src = question.img_url;
        } else if (fallbackSrc) {
            speakingImageEl.src = fallbackSrc;
        }
        speakingImageEl.alt = question.prompt_text || 'Question illustration';
    }

    const instruction = exercise.instruction || '';
    if (instructionSectionEl) {
        if (instruction) {
            instructionSectionEl.style.display = '';
            if (exerciseSubtitleEl) {
                exerciseSubtitleEl.textContent = instruction;
            }
        } else {
            instructionSectionEl.style.display = 'none';
        }
    }

    if (hintSectionEl && hintTextEl) {
        const hasHint = Boolean(question.starter_text);
        hintSectionEl.style.display = hasHint ? '' : 'none';
        if (hasHint) {
            hintTextEl.textContent = question.starter_text;
        }
    }

    if (wcsInputGroupEl && answerSuffixEl) {
        if (isWritingType && isWcs) {
            wcsInputGroupEl.style.display = 'flex';
            const prefix = (question.starter_text || '').trim();
            if (wcsPrefixEl) {
                wcsPrefixEl.textContent = prefix || '...';
            }
            answerSuffixEl.value = '';
            answerSuffixEl.placeholder = '...';
        } else {
            wcsInputGroupEl.style.display = 'none';
        }
    }

    if (answerField) {
        if (!isWritingType) {
            answerField.style.display = 'none';
            answerField.value = '';
        } else if (isWcs) {
            answerField.style.display = 'none';
            const prefix = (question.starter_text || '').trim();
            answerField.value = prefix ? `${prefix} ` : '';
        } else {
            answerField.style.display = '';
            answerField.value = '';
            answerField.placeholder = question.starter_text
                ? `${question.starter_text.trim()} ...`
                : 'Viết câu trả lời của con tại đây...';
        }
    }

    if (submitBtn) {
        submitBtn.style.display = isWritingType ? '' : 'none';
    }

    if (resetBtn) {
        resetBtn.style.display = isWritingType ? '' : 'none';
    }
}

async function setSampleAudio() {
    if (!sampleAudioBtn) {
        return;
    }

    const typeCode = (question?.exercise?.type?.code || '').toString().toUpperCase();
    if (!typeCode.startsWith('S')) {
        sampleAudioBtn.onclick = null;
        sampleAudioBtn.disabled = false;
        return;
    }

    try {
        let audioUrl = null;

        try {
            audioUrl = await questionApi.textToSpeech(question.prompt_text || '', 'en');
        } catch (ttsError) {
            console.warn('Không thể tạo audio bằng TTS, thử fallback', ttsError);
        }

        if (!audioUrl) {
            audioUrl = question.audio_url;
        }

        if (!audioUrl) {
            const fallback = sampleAudioBtn.dataset.defaultAudio;
            if (fallback) {
                audioUrl = fallback;
            }
        }

        if (!audioUrl) {
            throw new Error('Không tìm thấy nguồn audio hợp lệ');
        }

        if (sampleAudio) {
            try {
                sampleAudio.pause();
            } catch (error) {
                console.warn('Không thể dừng audio cũ', error);
            }

            if (sampleAudio.src && sampleAudio.src.startsWith('blob:')) {
                URL.revokeObjectURL(sampleAudio.src);
            }
        }

        sampleAudio = new Audio(audioUrl);
        sampleAudioBtn.disabled = false;
        sampleAudioBtn.onclick = () => {
            sampleAudio.currentTime = 0;
            sampleAudio.play().catch((error) => {
                console.warn('Không thể phát audio mẫu', error);
            });
        };
    } catch (err) {
        console.error('Không tạo được audio sample:', err);
        sampleAudioBtn.disabled = true;
        sampleAudioBtn.onclick = null;
    }
}

function pickMime() {
  if (window.MediaRecorder && MediaRecorder.isTypeSupported) {
    if (MediaRecorder.isTypeSupported('audio/webm')) return 'audio/webm';   
    if (MediaRecorder.isTypeSupported('audio/mp4'))  return 'audio/mp4';    
    if (MediaRecorder.isTypeSupported('audio/ogg'))  return 'audio/ogg';    
    if (MediaRecorder.isTypeSupported('audio/wav'))  return 'audio/wav';
  }
  return '';
}

async function startRec() {
    isRecording = true;
    if (recordingIndicator) {
        recordingIndicator.classList.add('is-active');
        recordingIndicator.style.display = 'inline-flex';
    }
    if (userAudio) {
        try {
            userAudio.pause();
        } catch (error) {
            /* ignore */
        }
        userAudio.style.display = 'none';
    }
    if (recordBtn) {
        recordBtn.classList.add('is-recording');
    }
    errorEl.style.display='none';
    resultCard.style.display='none';

    userAnswer = '';
    chunks = [];
    mediaStream = null;
    mediaRecorder = null;

    try {
        mediaStream = await navigator.mediaDevices.getUserMedia({
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
                channelCount: 1,
                sampleRate: 16000,       
                suppressLocalAudioPlayback: true, 
            }
        });
        const mime = pickMime();
        mediaRecorder = new MediaRecorder(
            mediaStream,
            mime ? { mimeType: mime,
                    audioBitsPerSecond: 128000
            } : undefined,
        );
        mediaSupported = true;

        mediaRecorder.ondataavailable = e => {
            if (e.data && e.data.size > 0) chunks.push(e.data);
        };
        mediaRecorder.start(300);
        console.log('MediaRecorder bắt đầu, mime =', mime || '(auto)');
    } catch (err) {
        console.warn('MediaRecorder không khả dụng:', err);
        mediaSupported = false;
    }

    if (Recognition) {
        recognition = new Recognition();
        recognition.lang = 'en-US';
        recognition.interimResults = true;
        recognition.continuous = true;

        recognition.onstart = () => console.log("[SpeechRecognition] started!");

        recognition.onresult = (e) => {
            console.log("test");
            
            let t = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                t += e.results[i][0].transcript;
            }
            userAnswer = t.trim();
            console.log("userAnswer: " + userAnswer);
        }

        recognition.onerror = (e) => console.error("[SpeechRecognition] error:", e.error);
        recognition.onend = () => console.log("[SpeechRecognition] ended");

        try {
            recognition.start();
            console.log("[SpeechRecognition] start() called!");
        } catch (err) {
            console.error("SpeechRecognition start error:", err);
        }
}}

async function transcribeAudio(audioBlob) {
    try {
        const res = await questionApi.transcribeAudio(audioBlob);
        console.log('Phản hồi từ speech-to-text API:', res);

        if (res.recognized_text) {
            userAnswer = res.recognized_text;
            console.log('Cập nhật userAnswer từ speech-to-text API:', userAnswer);
        } 
    } catch (e) {
        console.error('Chuyển đổi ghi âm không thành công:', e);
    }
}

async function stopRec() {
    isRecording = false;

    if (recordBtn) {
        recordBtn.classList.remove('is-recording');
    }

    if (recordingIndicator) {
        recordingIndicator.classList.remove('is-active');
        recordingIndicator.style.display = 'none';
    }

    if (recognition) { try { recognition.stop(); } catch {} recognition = null; }

    let audioBlob = null;
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        const stopped = new Promise(resolve => {
        mediaRecorder.addEventListener('stop', resolve, { once: true });
        });
        try { mediaRecorder.requestData(); } catch {}
        mediaRecorder.stop();
        await stopped;

        try { mediaStream?.getTracks().forEach(t => t.stop()); } catch {}

        const mime = (chunks[0]?.type) || mediaRecorder.mimeType || 'audio/webm';
        audioBlob = chunks.length ? new Blob(chunks, { type: mime }) : null;

        if (audioBlob) {
        const url = URL.createObjectURL(audioBlob);
        console.log('blob size =', audioBlob.size, 'type =', audioBlob.type);
        } else {
            console.warn('Không có âm thanh được ghi.');
        }
    }

    if (userAudio && audioBlob) {
        userAudio.style.display = 'block';
    }

    if ((!userAnswer || userAnswer.trim() === '') && mediaSupported && audioBlob) {
        const res = await transcribeAudio(audioBlob);
        console.log('Kết quả chuyển đổi từ audio:', res);
    }

    submitAnswer(userAnswer, audioBlob);
}

function playUiSound(type) {
    const src = SOUND_MAP[type];
    if (!src) {
        return;
    }

    if (!audioCache[type]) {
        const audio = new Audio(src);
        audio.load();
        audioCache[type] = audio;
    }

    const audio = audioCache[type];
    try {
        audio.currentTime = 0;
        audio.play().catch(() => {});
    } catch (error) {
        console.warn('[writing-question] Failed to play sound', type, error);
    }
}

function validateAnswer(answer) {
    if (question.exercise.type.code.includes('WCS')) {
        if (answerSuffixEl) {
            const suffix = answerSuffixEl.value.trim();
            if (!suffix) {
                showError('Con hãy hoàn thành câu trước khi gửi nhé!');
                return;
            }
            const prefix = question.starter_text ? question.starter_text.trim() : '';
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
}

// Submit answer
async function submitAnswer(userAnswer='', audioBlob=null) {
    if (question.exercise.type.code.includes('W')) {
        validateAnswer(userAnswer);
        userAnswer = answerField.value;

        console.log("User answer: " + userAnswer);

        if (isEmpty(userAnswer)) {
            showError('Vui lòng nhập câu trả lời.');
        return;
        }

        if (submitBtn) {
            if (!submitBtn.dataset.originalContent) {
                submitBtn.dataset.originalContent = submitBtn.innerHTML;
            }
            const loadingText = submitBtn.dataset.loadingText || 'Đang chấm bài...';
            submitBtn.innerHTML = `<span class="btn-loader"></span><span>${loadingText}</span>`;
            submitBtn.classList.add('is-loading');
            submitBtn.disabled = true;
        }
        if (loadingEl) loadingEl.style.display = 'flex';
        if (errorEl) errorEl.style.display = 'none';
    } else {
        if (!userAnswer || userAnswer.trim() === '') {
            showError('Vui lòng đọc nội dung.');
            recordingIndicator.style.display='none';
            return;
        }

        console.log("User answer: " + userAnswer);

        userAudio.src = URL.createObjectURL(audioBlob);
        userAudio.style.display = 'block';
    }

    playUiSound('button');

    if (resultCard) resultCard.style.display = 'none';

    try {
        const response = await questionApi.evaluateAnswer(userId, question.id, userAnswer, audioBlob);

        renderResult(response.data);
    } catch (error) {
        showError(error.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('is-loading');
            submitBtn.innerHTML = submitBtn.dataset.originalContent || 'Gửi câu trả lời';
        }
        if (loadingEl) loadingEl.style.display = 'none';
    }
}

function resetAnswer() {
    if (question.exercise.type.code === 'WCS') {
        if (answerSuffixEl) {
            answerSuffixEl.value = '';
            answerSuffixEl.placeholder = '...';
            answerSuffixEl.focus();
        }
        if (answerField) {
            answerField.value = question.starter_text ? `${question.starter_text.trim()} ` : '';
        }
    } else if (answerField) {
        answerField.value = '';
        answerField.placeholder = question.starter_text
            ? `${question.starter_text.trim()} ...`
            : 'Viết câu trả lời của con tại đây...';
        answerField.focus();
    } else if (recordBtn) {
        userAudio.style.display = 'none';
    }

    errorEl.style.display = 'none';
    resultCard.style.display = 'none';
}

function renderResult(data) {
    if (recordingIndicator) {
        recordingIndicator.classList.remove('is-active');
        recordingIndicator.style.display = 'none';
    }

    const stage = data.is_correct ? 'success' : 'failure';
    const statusText = data.is_correct ? 'Làm tốt lắm!' : 'Cùng thử lại nhé';
    const statusClass = data.is_correct ? 'success' : 'failure';
    const highlightHtml = Feedback.renderHighlights ? Feedback.renderHighlights(data) : '';
    const notesHtml = Feedback.renderNotes ? Feedback.renderNotes(data) : '';

    let feedbackText = null;
    let score = null;
    let numericScore = null;

    const userWrittenAnswer = answerField?.value ?? '';
    let rawFeedback = data.feedback || '';

    if(question.exercise.type.code.includes('W')) {
        rawFeedback = sanitizeFeedbackMessage(rawFeedback, userWrittenAnswer);
        numericScore = Number.isFinite(data.score) ? Number(data.score) : extractScoreFromFeedback(rawFeedback);
        score = Number.isFinite(numericScore) ? `${numericScore}/100` : 'N/A';
        feedbackText = Feedback.formatFeedbackForKids ? Feedback.formatFeedbackForKids(rawFeedback) : rawFeedback;
    } else {
        feedbackText = data.feedback;
        if(data.is_correct) {
            score = "100/100";
            numericScore = 100;
        } else {
            score = "0/100";
            numericScore = 0;
        }
    }

    resultFeedback.innerHTML = `
        <div class="status-row">
            <span class="status-pill ${statusClass}">✨ ${statusText}</span>
            <span class="score-pill">Điểm: ${score}</span>
        </div>
        <div class="result-text">${feedbackText}</div>
        ${notesHtml ? `<div class="hl-container">${notesHtml}</div>` : ''}
        <div class="result-actions">
            <button id="do-again-btn" class="btn-primary" type="button">Làm thêm lần nữa</button>
        </div>
    `;

    
    const doAgainBtn = document.getElementById('do-again-btn');
    doAgainBtn.addEventListener('click', resetAnswer)

    resultCard.style.display = 'block';
    resultCard.scrollIntoView({ behavior: 'smooth' });
    playUiSound(stage);

    if (stage === 'success' || (Number.isFinite(numericScore) && numericScore >= 80)) {
        triggerSuccessEffect(numericScore);
    }
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').style.display = 'block';
}

function setNavigationBtnUrl() {
    if (!prevBtn || !nextBtn) return;

    const hasPrev = Boolean(question.prev_question_id);
    const hasNext = Boolean(question.next_question_id);

    prevBtn.disabled = !hasPrev;
    prevBtn.dataset.target = hasPrev ? question.prev_question_id : '';
    nextBtn.disabled = !hasNext;
    nextBtn.dataset.target = hasNext ? question.next_question_id : '';

    if (questionCounter) {
        if (totalQuestions > 0) {
            questionCounter.textContent = `(${currentQuestionIndex + 1}/${totalQuestions})`;
        } else {
            questionCounter.textContent = '(—)';
        }
    }

    if (questionSection) {
        questionSection.classList.toggle('nav-single', totalQuestions <= 1);
    }
}

function handleQuestionSelect(questionId) {
    if (!questionId) return;
    window.location.assign(`/embed/question/${questionId}`);
}

function initEventListeners() {
    if (submitBtn) {
        submitBtn.addEventListener('click', () => submitAnswer());
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', resetAnswer);
    }

    if (questionSelect) {
        questionSelect.addEventListener('change', (e) => {
            handleQuestionSelect(e.target.value);
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (prevBtn.dataset.target) {
                handleQuestionSelect(prevBtn.dataset.target);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (nextBtn.dataset.target) {
                handleQuestionSelect(nextBtn.dataset.target);
            }
        });
    }

    if (recordBtn) {
        recordBtn.addEventListener('click', () => {
            if (isRecording) {
                stopRec();
            } else {
                startRec();
            }
        });
    }
}

function initQuestionPage() {
    hydrateQuestionContent();
    initTranslationFeature();
    initEventListeners();
    setNavigationBtnUrl();
    if (sampleAudioBtn)  setSampleAudio();
    initNavigationSelectors();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuestionPage);
} else {
    initQuestionPage();
}

