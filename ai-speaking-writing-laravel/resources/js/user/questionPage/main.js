//import './utils/translationBySelectText'
import { isEmpty } from 'lodash';
import questionApi from '../../api/questionApi';
import { initTranslationFeature } from './translationBySelectText';
import Feedback from './writing-feedback';
import {
    getExerciseType,
    clearUserAudio,
    setLoadingState,
    triggerSuccessEffect,
    sanitizeFeedbackMessage,
    extractScoreFromFeedback,
    toNumber
} from './helpers';
import * as Navigation from './navigation';

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
// Exercise selector removed - no longer needed
const exerciseSelect = null;
const writingWrapper = document.getElementById('writingAnswerWrapper');
const speakingWrapper = document.getElementById('speakingAnswerWrapper');

const loadingEl = document.getElementById('loading');
const errorEl = document.getElementById('error');

const resultCard = document.getElementById('resultCard');
const answerField = document.getElementById('userAnswer');
const answerSuffixEl = document.getElementById('answerSuffix');
const resultFeedback = document.getElementById('resultFeedback');

// Popup elements
const loadingPopup = document.getElementById('loadingPopup');
const resultPopup = document.getElementById('resultPopup');
const resultPopupContent = document.getElementById('resultPopupContent');
const resultPopupClose = document.getElementById('resultPopupClose');
let resultPopupTimeout = null;
const questionCounter = document.getElementById('questionCounter');
const questionCounterBanner = document.getElementById('questionCounterBanner');
const questionSection = document.querySelector('.nav-section--question');
const exerciseTitleHeadingEl = document.getElementById('exerciseTitleHeading');
const exerciseTitleEl = document.getElementById('exerciseTitle');
const exerciseSubtitleEl = document.getElementById('instructionText');
const instructionSectionEl = document.getElementById('instructionSection');
const instructionToggleBtn = document.getElementById('instructionToggleBtn');
const instructionPopup = document.getElementById('instructionPopup');
const instructionPopupClose = document.getElementById('instructionPopupClose');
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

// Get questions from allQuestions if available, otherwise try to get from exercise relationship
let exerciseQuestions = [];
if (window.appData.allQuestions && Array.isArray(window.appData.allQuestions)) {
    exerciseQuestions = window.appData.allQuestions;
} else if (Array.isArray(question?.exercises?.[0]?.questions)) {
    exerciseQuestions = question.exercises[0].questions;
} else if (currentContext.exercise_id) {
    // Fallback: try to get from navigation data
    const exerciseNav = navigationData
        .flatMap(type => type.lessons || [])
        .flatMap(lesson => lesson.exercises || [])
        .find(ex => ex.id === currentContext.exercise_id);
    if (exerciseNav && exerciseNav.questions) {
        exerciseQuestions = exerciseNav.questions;
    }
}

const totalQuestions = exerciseQuestions.length;
const currentQuestionIndex = Math.max(
    0,
    exerciseQuestions.findIndex((q) => q.id === question.id)
);

// Helper function to build embed URL from question_id or exercise_id
function buildEmbedUrl(questionId, exerciseId = null, exerciseType = null) {
    // Use new route format: /embed-writing/exercises/{exercise} or /embed-speaking/exercises/{exercise}
    const finalExerciseId = exerciseId || question?.exercises?.[0]?.id || currentContext.exercise_id;
    const finalExerciseType = exerciseType || question?.exercises?.[0]?.type?.code || currentContext.type;
    
    if (finalExerciseId && finalExerciseType) {
        const typeCode = String(finalExerciseType).toUpperCase();
        const routePrefix = typeCode.startsWith('W') ? '/embed-writing/exercises' : '/embed-speaking/exercises';
        const url = `${routePrefix}/${finalExerciseId}`;
        return questionId ? `${url}?questionId=${questionId}` : url;
    }
    
    // If we don't have exercise info, we can't build the URL
    console.warn('Cannot build embed URL: missing exercise_id or exercise_type');
    return null;
}

const ANSWER_STORAGE_NAMESPACE = `questionAnswers:${userId || 'guest'}`;
let inMemoryAnswerCache = {};

const answerStorage = (() => {
    try {
        if (typeof window !== 'undefined' && window.sessionStorage) {
            const testKey = '__qa_test__';
            window.sessionStorage.setItem(testKey, '1');
            window.sessionStorage.removeItem(testKey);
            return window.sessionStorage;
        }
    } catch (error) {
        console.warn('[questionPage] SessionStorage unavailable, fallback to memory cache.', error);
    }
    return null;
})();

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

// Helper functions đã được tách ra helpers.js

function readStoredAnswers() {
    if (answerStorage) {
        try {
            const raw = answerStorage.getItem(ANSWER_STORAGE_NAMESPACE);
            return raw ? JSON.parse(raw) : {};
        } catch (error) {
            console.warn('[questionPage] Failed to parse stored answers', error);
            return {};
        }
    }
    return inMemoryAnswerCache;
}

function writeStoredAnswers(state) {
    if (answerStorage) {
        try {
            answerStorage.setItem(ANSWER_STORAGE_NAMESPACE, JSON.stringify(state));
        } catch (error) {
            console.warn('[questionPage] Failed to persist answers', error);
        }
    } else {
        inMemoryAnswerCache = state;
    }
}

function getStoredAnswer(questionId) {
    if (!questionId) return null;
    const state = readStoredAnswers();
    return state[String(questionId)] || null;
}

function rememberAnswer(questionId, payload) {
    if (!questionId || !payload) return;
    const state = { ...readStoredAnswers(), [String(questionId)]: payload };
    writeStoredAnswers(state);
}

function clearStoredAnswer(questionId) {
    if (!questionId) return;
    const state = { ...readStoredAnswers() };
    if (Object.prototype.hasOwnProperty.call(state, String(questionId))) {
        delete state[String(questionId)];
        writeStoredAnswers(state);
    }
}

function clearStoredAnswersForExercise() {
    if (!Array.isArray(exerciseQuestions) || !exerciseQuestions.length) {
        return;
    }
    exerciseQuestions.forEach((item) => {
        if (item?.id) {
            clearStoredAnswer(item.id);
        }
    });
}

function hydrateStoredAnswer() {
    const type = getExerciseType(question);
    if (!type.isWriting) return;

    const stored = getStoredAnswer(question.id);
    if (!stored) return;

    if (type.isWcs) {
        if (answerSuffixEl && typeof stored.suffix === 'string') {
            answerSuffixEl.value = stored.suffix;
        }
        if (answerField && typeof stored.text === 'string') {
            answerField.value = stored.text;
        }
    } else if (answerField && typeof stored.text === 'string') {
        answerField.value = stored.text;
    }
}

let activeTypeCode = currentContext.type || (question?.exercises?.[0]?.type?.code ?? (navigationData[0]?.code ?? null));
let activeLessonId = toNumber(currentContext.lesson_id ?? question?.exercises?.[0]?.lesson_id);
let activeExerciseId = toNumber(currentContext.exercise_id ?? question?.exercises?.[0]?.id);

// Navigation functions đã được tách ra navigation.js

function initNavigationSelectors() {
    if (!navigationData.length) return;

    if (!activeTypeCode) {
        activeTypeCode = navigationData[0]?.code ?? null;
    }

    // Initialize: hiển thị tất cả lessons và types
    Navigation.updateLessonOptions(activeTypeCode, activeLessonId, lessonSelect, navigationData);
    Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);

    // Đảm bảo activeLessonId và activeTypeCode có thể kết hợp được
    if (activeLessonId && activeTypeCode) {
        const availableTypes = Navigation.getAvailableTypesForLesson(activeLessonId, navigationData);
        const availableLessons = Navigation.getAvailableLessonsForType(activeTypeCode, navigationData);
        
        if (availableLessons.length > 0 && !availableLessons.some((l) => Number(l.id) === activeLessonId)) {
            const firstLessonWithTypes = availableLessons.find((lesson) => Navigation.lessonHasTypes(Number(lesson.id), navigationData));
            if (firstLessonWithTypes) {
                activeLessonId = Number(firstLessonWithTypes.id);
                if (lessonSelect) lessonSelect.value = String(activeLessonId);
                Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
            }
        }
        
        if (availableTypes.length > 0 && !availableTypes.some((t) => t.code === activeTypeCode)) {
            activeTypeCode = availableTypes[0].code;
            if (typeSelect) typeSelect.value = activeTypeCode;
        }
    } else if (activeLessonId) {
        const availableTypes = Navigation.getAvailableTypesForLesson(activeLessonId, navigationData);
        if (availableTypes.length > 0) {
            activeTypeCode = availableTypes[0].code;
            if (typeSelect) typeSelect.value = activeTypeCode;
        }
        Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
    } else if (activeTypeCode) {
        const availableLessons = Navigation.getAvailableLessonsForType(activeTypeCode, navigationData);
        const firstLessonWithTypes = availableLessons.find((lesson) => Navigation.lessonHasTypes(Number(lesson.id), navigationData));
        if (firstLessonWithTypes) {
            activeLessonId = Number(firstLessonWithTypes.id);
            if (lessonSelect) lessonSelect.value = String(activeLessonId);
        }
        Navigation.updateLessonOptions(activeTypeCode, activeLessonId, lessonSelect, navigationData);
        Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', (e) => {
            const selectedType = e.target.value;
            if (!selectedType || selectedType === activeTypeCode) return;

            activeTypeCode = selectedType;
            activeExerciseId = null;
            Navigation.updateLessonOptions(activeTypeCode, activeLessonId, lessonSelect, navigationData);
            
            const availableLessons = Navigation.getAvailableLessonsForType(activeTypeCode, navigationData);
            const currentLessonInType = availableLessons.find((lesson) => Number(lesson.id) === activeLessonId);
            
            if (currentLessonInType && Navigation.lessonHasTypes(activeLessonId, navigationData)) {
                Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
                const questionId = Navigation.getFirstQuestionIdFromLesson(activeTypeCode, activeLessonId, navigationData);
                if (questionId) {
                    const url = buildEmbedUrl(questionId);
                    window.location.assign(url);
                }
            } else {
                const firstLessonWithTypes = availableLessons.find((lesson) => Navigation.lessonHasTypes(Number(lesson.id), navigationData));
                if (firstLessonWithTypes) {
                    activeLessonId = Number(firstLessonWithTypes.id);
                    if (lessonSelect) lessonSelect.value = String(activeLessonId);
                    Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
                    const questionId = Navigation.getFirstQuestionIdFromLesson(activeTypeCode, activeLessonId, navigationData);
                    if (questionId) {
                    const url = buildEmbedUrl(questionId);
                    window.location.assign(url);
                }
                } else {
                    Navigation.renderTypeOptions(null, activeTypeCode, typeSelect, navigationData);
                    const questionId = Navigation.getFirstQuestionIdForType(activeTypeCode, navigationData);
                    if (questionId) {
                    const url = buildEmbedUrl(questionId);
                    window.location.assign(url);
                }
                }
            }
        });
    }

    if (lessonSelect) {
        lessonSelect.addEventListener('change', (e) => {
            const selectedLessonId = toNumber(e.target.value);
            if (!selectedLessonId || selectedLessonId === activeLessonId) return;
            
            if (!Navigation.lessonHasTypes(selectedLessonId, navigationData)) {
                console.warn('Lesson does not have any types');
                activeLessonId = selectedLessonId;
                Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
                return;
            }

            activeLessonId = selectedLessonId;
            activeExerciseId = null;
            Navigation.renderTypeOptions(activeLessonId, activeTypeCode, typeSelect, navigationData);
            
            const availableTypes = Navigation.getAvailableTypesForLesson(activeLessonId, navigationData);
            if (availableTypes.length > 0) {
                if (!availableTypes.some((type) => type.code === activeTypeCode)) {
                    activeTypeCode = availableTypes[0].code;
                    if (typeSelect) typeSelect.value = activeTypeCode;
                }
            } else {
                console.warn('Lesson does not have any available types');
            }
            
            const questionId = Navigation.getFirstQuestionIdFromLesson(activeTypeCode, activeLessonId, navigationData);
            if (questionId) {
                // Redirect to /questions/{id} which will automatically redirect to new embed route
                window.location.assign(`/questions/${questionId}`);
            }
        });
    }
}

function hydrateQuestionContent() {
    if (!question) return;

    const exercise = question?.exercises?.[0] || {};
    const exerciseType = exercise.type || {};
    const lesson = exercise.lesson || {};
    const type = getExerciseType(question);
    const isWritingType = type.isWriting;
    const isSpeakingType = type.isSpeaking;
    const isWcs = type.isWcs;
    const rawTypeCode = type.code;

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

    const type = getExerciseType(question);
    if (!type.isSpeaking) {
        sampleAudioBtn.onclick = null;
        sampleAudioBtn.disabled = false;
        return;
    }

    try {
        let audioUrl = null;
        let ttsSuccess = false;

        // Ưu tiên TTS: luôn thử TTS trước
        const promptText = question.prompt_text || '';
        if (promptText && promptText.trim()) {
            try {
                console.log('🎙️ Đang tạo audio bằng TTS cho:', promptText.substring(0, 50) + '...');
                const ttsUrl = await questionApi.textToSpeech(promptText.trim(), 'en');
                
                // Kiểm tra xem TTS có thành công không
                if (ttsUrl && typeof ttsUrl === 'string' && ttsUrl.length > 0 && ttsUrl.startsWith('blob:')) {
                    ttsSuccess = true;
                    audioUrl = ttsUrl;
                    console.log('✅ TTS thành công! URL:', ttsUrl.substring(0, 50) + '...');
                } else {
                    console.warn('⚠️ TTS trả về URL không hợp lệ:', ttsUrl);
                    ttsSuccess = false;
                }
            } catch (ttsError) {
                console.warn('❌ TTS thất bại, chi tiết lỗi:', {
                    message: ttsError.message,
                    status: ttsError.response?.status,
                    data: ttsError.response?.data
                });
                audioUrl = null;
                ttsSuccess = false;
            }
        } else {
            console.warn('⚠️ Không có prompt_text để dùng TTS');
            ttsSuccess = false;
        }

        // Chỉ fallback nếu TTS thất bại
        if (!ttsSuccess || !audioUrl) {
            console.log('🔄 Fallback sang audio_url hoặc default audio');
            
            // Fallback 1: question.audio_url
            if (question.audio_url && question.audio_url.trim()) {
                audioUrl = question.audio_url;
                console.log('✅ Dùng question.audio_url:', audioUrl);
            } 
            // Fallback 2: data-default-audio
            else {
                const fallback = sampleAudioBtn.dataset.defaultAudio;
                if (fallback && fallback.trim()) {
                    audioUrl = fallback;
                    console.log('✅ Dùng default audio:', audioUrl);
                }
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
        
        // Test audio có load được không
        sampleAudio.onerror = (error) => {
            console.error('❌ Audio không thể load:', error, 'URL:', audioUrl);
            // Nếu TTS fail và audio_url cũng fail, thử default
            if (ttsSuccess || question.audio_url) {
                const fallback = sampleAudioBtn.dataset.defaultAudio;
                if (fallback && fallback !== audioUrl) {
                    console.log('🔄 Thử fallback cuối cùng:', fallback);
                    sampleAudio = new Audio(fallback);
                    sampleAudio.onerror = () => {
                        console.error('❌ Tất cả audio sources đều fail');
                        sampleAudioBtn.disabled = true;
                    };
                }
            }
        };
        
        sampleAudioBtn.disabled = false;
        sampleAudioBtn.onclick = () => {
            try {
                sampleAudio.currentTime = 0;
                sampleAudio.play().catch((error) => {
                    console.warn('Không thể phát audio mẫu', error, 'URL:', sampleAudio.src);
                });
            } catch (error) {
                console.error('Lỗi khi phát audio:', error);
            }
        };
    } catch (err) {
        console.error('❌ Không tạo được audio sample:', err);
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
    if (errorEl) errorEl.style.display='none';
    if (resultCard) resultCard.style.display='none';

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
            let t = '';
            // Lấy tất cả kết quả từ đầu đến cuối để có đầy đủ text
            for (let i = 0; i < e.results.length; i++) {
                if (e.results[i].isFinal) {
                    // Kết quả cuối cùng - ưu tiên
                    t = e.results[i][0].transcript;
                } else {
                    // Kết quả tạm thời
                    t += e.results[i][0].transcript;
                }
            }
            
            // Nếu không có kết quả final, dùng tất cả transcript
            if (!t) {
                for (let i = e.resultIndex; i < e.results.length; i++) {
                    t += e.results[i][0].transcript;
                }
            }
            
            userAnswer = t.trim();
            console.log("userAnswer from SpeechRecognition: " + userAnswer);
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

    // Lưu userAnswer cuối cùng trước khi stop recognition
    let savedUserAnswer = userAnswer || '';
    
    if (recognition) { 
        try { 
            recognition.stop(); 
        } catch (e) {
            console.warn('Error stopping recognition:', e);
        }
        // Đợi một chút để SpeechRecognition có thể hoàn tất onresult cuối cùng
        await new Promise(resolve => setTimeout(resolve, 100));
        recognition = null; 
    }
    
    // Dùng savedUserAnswer nếu userAnswer vẫn rỗng
    if (!userAnswer || userAnswer.trim() === '') {
        userAnswer = savedUserAnswer;
        console.log('Using savedUserAnswer:', userAnswer);
    }

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
            
            // Set userAudio để có thể nghe lại
            if (userAudio) {
                userAudio.src = url;
                userAudio.style.display = 'block';
            }
        } else {
            console.warn('Không có âm thanh được ghi.');
        }
    }

    // Kiểm tra nếu không có audioBlob hoặc audioBlob quá nhỏ (có thể không có tiếng nói)
    const MIN_AUDIO_SIZE = 1000; // 1KB - ngưỡng tối thiểu để coi là có audio
    if (!audioBlob || audioBlob.size < MIN_AUDIO_SIZE) {
        console.warn('Không có audio hoặc audio quá nhỏ', {
            hasAudioBlob: !!audioBlob,
            audioBlobSize: audioBlob?.size || 0,
            userAnswer: userAnswer
        });
        if (errorEl) {
            showError('Bạn chưa ghi âm hoặc không có tiếng nói. Vui lòng bấm nút mic và đọc câu trả lời rõ ràng.');
        }
        if (recordingIndicator) {
            recordingIndicator.style.display = 'none';
        }
        clearUserAudio(userAudio);
        return;
    }

    // Nếu SpeechRecognition không nhận được text, dùng STT API
    if ((!userAnswer || userAnswer.trim() === '') && mediaSupported && audioBlob) {
        console.log('SpeechRecognition không nhận được text, dùng STT API...');
        await transcribeAudio(audioBlob);
        console.log('userAnswer sau STT:', userAnswer);
    }

    // Kiểm tra lại userAnswer trước khi submit
    const finalUserAnswer = userAnswer || savedUserAnswer || '';
    if (!finalUserAnswer || finalUserAnswer.trim() === '') {
        console.warn('Không có userAnswer để submit', {
            userAnswer, savedUserAnswer, audioBlobSize: audioBlob?.size || 0
        });
        showError('Không nhận được giọng nói. Vui lòng đọc lại rõ ràng và đầy đủ hơn.');
        if (recordingIndicator) recordingIndicator.style.display = 'none';
        clearUserAudio(userAudio);
        return;
    }

    console.log('Submitting answer:', finalUserAnswer, 'blob size:', audioBlob?.size || 0);
    submitAnswer(finalUserAnswer, audioBlob);
}

function playUiSound(type) {
    const src = SOUND_MAP[type];
    if (!src) {
        return;
    }

    if (!audioCache[type]) {
        const audio = new Audio(src);
        audio.volume = 0.5; // Giảm volume xuống 50%
        audio.load();
        audioCache[type] = audio;
    }

    const audio = audioCache[type];
    try {
        audio.currentTime = 0;
        audio.volume = 0.5; // Đảm bảo volume luôn là 50%
        audio.play().catch(() => {});
    } catch (error) {
        console.warn('[writing-question] Failed to play sound', type, error);
    }
}

function validateAnswer(answer) {
    const type = getExerciseType(question);
    if (!type.isWriting) {
        return; // Not a writing exercise, skip validation
    }
    
    const isWcs = type.isWcs;
    
    if (isWcs) {
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
async function submitAnswer(answerText='', audioBlob=null) {
    const type = getExerciseType(question);
    let finalAnswer = answerText || userAnswer;
    
    if (type.isWriting) {
        validateAnswer(finalAnswer);
        finalAnswer = answerField?.value || finalAnswer;
        console.log("User answer (Writing): " + finalAnswer);

        if (isEmpty(finalAnswer)) {
            showError('Vui lòng nhập câu trả lời.');
            return;
        }
        rememberAnswer(question.id, {
            text: answerField?.value ?? finalAnswer,
            suffix: type.isWcs && answerSuffixEl ? answerSuffixEl.value : null,
            type: type.code,
            updatedAt: Date.now(),
        });
        setLoadingState(true, submitBtn, loadingEl, errorEl, loadingPopup);
    } else if (type.isSpeaking) {
        console.log("Speaking exercise detected, type:", type.code);
        
        if (!finalAnswer || finalAnswer.trim() === '') {
            showError('Vui lòng đọc nội dung.');
            if (recordingIndicator) recordingIndicator.style.display = 'none';
            return;
        }

        if (!audioBlob) {
            showError('Không có file ghi âm. Vui lòng ghi âm lại.');
            return;
        }

        console.log("User answer (Speaking): " + finalAnswer);
        if (userAudio && !userAudio.src) {
            userAudio.src = URL.createObjectURL(audioBlob);
        }
        if (userAudio) userAudio.style.display = 'block';
        setLoadingState(true, submitBtn, loadingEl, errorEl, loadingPopup);
    } else {
        showError('Không xác định được loại bài tập.');
        return;
    }

    playUiSound('button');
    if (resultCard) resultCard.style.display = 'none';

    try {
        const response = await questionApi.evaluateAnswer(userId, question.id, finalAnswer, audioBlob);
        // Mark question as completed after successful submission
        markQuestionCompleted(question.id);
        renderResult(response.data);
    } catch (error) {
        showError(error.message);
    } finally {
        setLoadingState(false, submitBtn, loadingEl, errorEl, loadingPopup);
    }
}

function resetAnswer() {
    const type = getExerciseType(question);
    clearStoredAnswer(question.id);
    
    if (type.isWriting) {
        if (type.isWcs) {
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
        }
    } else if (type.isSpeaking) {
        clearUserAudio(userAudio);
        userAnswer = '';
        if (recordingIndicator) {
            recordingIndicator.style.display = 'none';
            recordingIndicator.classList.remove('is-active');
        }
        if (recordBtn) {
            recordBtn.classList.remove('is-recording');
        }
    }

    if (errorEl) errorEl.style.display = 'none';
    if (resultCard) resultCard.style.display = 'none';
}

function openResultPopup() {
    if (resultPopup) {
        resultPopup.classList.add('show');
    }
}

function closeResultPopup() {
    if (resultPopup) {
        resultPopup.classList.remove('show');
    }
    if (resultPopupTimeout) {
        clearTimeout(resultPopupTimeout);
        resultPopupTimeout = null;
    }
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

    const type = getExerciseType(question);
    const userWrittenAnswer = answerField?.value ?? '';
    let rawFeedback = data.feedback || '';

    if (type.isWriting) {
        rawFeedback = sanitizeFeedbackMessage(rawFeedback, userWrittenAnswer);
        numericScore = Number.isFinite(data.score) ? Number(data.score) : extractScoreFromFeedback(rawFeedback);
        score = Number.isFinite(numericScore) ? `${numericScore}/100` : 'N/A';
        feedbackText = Feedback.formatFeedbackForKids ? Feedback.formatFeedbackForKids(rawFeedback) : rawFeedback;
    } else if (type.isSpeaking) {
        // Speaking exercise (SPS, SPW)
        feedbackText = data.feedback || '';
        // Use score from backend if available, otherwise calculate from is_correct
        if (Number.isFinite(data.score)) {
            numericScore = Number(data.score);
            score = `${numericScore}/100`;
        } else if (data.is_correct !== null && data.is_correct !== undefined) {
            numericScore = data.is_correct ? 100 : 0;
            score = `${numericScore}/100`;
        } else {
            score = "N/A";
            numericScore = null;
        }
    } else {
        // Fallback
        feedbackText = data.feedback || '';
        score = "N/A";
        numericScore = null;
    }

    // Render to popup
    if (resultPopupContent) {
        // Calculate next question from exerciseQuestions array
        const currentIndex = exerciseQuestions.findIndex((q) => q.id === question.id);
        const nextQuestionId = (currentIndex >= 0 && currentIndex < exerciseQuestions.length - 1) 
            ? exerciseQuestions[currentIndex + 1].id 
            : (question.next_question_id || null);
        const hasNext = Boolean(nextQuestionId);
        
        resultPopupContent.innerHTML = `
            <div class="status-row">
                <span class="status-pill ${statusClass}">✨ ${statusText}</span>
                <span class="score-pill">Điểm: ${score}</span>
            </div>
            <div class="result-text">${feedbackText}</div>
            ${notesHtml ? `<div class="hl-container">${notesHtml}</div>` : ''}
            <div class="result-actions">
                <button id="do-again-btn" class="btn-primary" type="button">Làm thêm lần nữa</button>
                ${hasNext ? `<button id="popup-next-btn" class="btn-secondary" type="button">Câu sau</button>` : ''}
            </div>
        `;

        // Add event listeners
        const doAgainBtn = document.getElementById('do-again-btn');
        if (doAgainBtn) {
            doAgainBtn.addEventListener('click', () => {
                closeResultPopup();
                resetAnswer();
            });
        }

        const popupNextBtn = document.getElementById('popup-next-btn');
        if (popupNextBtn && nextQuestionId) {
            popupNextBtn.addEventListener('click', () => {
                closeResultPopup();
                handleQuestionSelect(nextQuestionId);
            });
        }

        openResultPopup();

        // Check statistics immediately after showing result
        // Add small delay to ensure attempt is saved in database
        setTimeout(() => {
            checkAndShowExerciseStatistics();
        }, 1000);

        // Auto-close after 5 seconds
        if (resultPopupTimeout) {
            clearTimeout(resultPopupTimeout);
        }
        resultPopupTimeout = setTimeout(() => {
            closeResultPopup();
        }, 5000);
    }

    // Fallback to inline result card if popup not available
    if (resultFeedback && resultCard) {
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
        if (doAgainBtn) {
            doAgainBtn.addEventListener('click', resetAnswer);
        }
        resultCard.style.display = 'block';
        resultCard.scrollIntoView({ behavior: 'smooth' });
    }

    playUiSound(stage);

    if (stage === 'success' || (Number.isFinite(numericScore) && numericScore >= 80)) {
        triggerSuccessEffect(numericScore);
    }
}

async function checkAndShowExerciseStatistics() {
    try {
        // Get exercise_id from currentContext or question
        const exerciseId = currentContext.exercise_id || question?.exercises?.[0]?.id;
        
        if (!exerciseId) {
            console.log('No exercise_id found, skipping statistics check');
            return;
        }
        
        // Get lesson_id for reset mode (still use lesson for reset mode)
        const lessonId = currentContext.lesson_id || question?.exercises?.[0]?.lesson_id;
        
        // Check reset mode (still based on lesson)
        const resetMode = lessonId ? getResetMode(lessonId) : { isActive: false, timestamp: null };
        
        if (resetMode.isActive) {
            console.log(`Reset mode active for lesson ${lessonId} - only counting attempts after reset timestamp: ${resetMode.timestamp}`);
        }
        
        console.log('Checking exercise statistics for exercise_id:', exerciseId, 'user_id:', userId);
        
        // Get exercise statistics with reset timestamp if in reset mode
        const response = await questionApi.getExerciseStatistics(exerciseId, userId, resetMode.timestamp);
        
        console.log('Statistics response:', response);
        
        if (response.status === 'success' && response.data) {
            const stats = response.data;
            
            console.log('Statistics data:', stats);
            console.log('Completed:', stats.completed_questions, 'Total:', stats.total_questions);
            
            const isCompleted = stats.completed_questions > 0 && stats.completed_questions === stats.total_questions;
            
            // Reset mode: only show statistics if all questions completed again with NEW attempts
            if (resetMode.isActive && isCompleted) {
                console.log('All questions completed again after reset! Removing flag and showing statistics');
                if (lessonId) clearResetMode(lessonId);
                showExerciseStatistics(stats);
            } 
            // Reset mode: hide statistics until all questions completed again
            else if (resetMode.isActive) {
                console.log('Reset mode: hiding statistics until all questions are completed again');
                hideStatisticsSection();
                // Ensure answer wrapper is shown
                const exerciseType = getExerciseTypeFromQuestion();
                toggleAnswerWrappers(exerciseType, true);
            }
            // Normal mode: show statistics if all questions completed
            else if (isCompleted) {
                console.log('All questions completed! Showing statistics section');
                showExerciseStatistics(stats);
            } else {
                console.log('Not all questions completed yet. Completed:', stats.completed_questions, 'Total:', stats.total_questions);
                hideStatisticsSection();
                // Ensure answer wrapper is shown when not completed
                const exerciseType = getExerciseTypeFromQuestion();
                toggleAnswerWrappers(exerciseType, true);
            }
        }
    } catch (error) {
        console.error('Error getting exercise statistics:', error);
    }
}

// Store exercise type before hiding wrappers
let savedExerciseType = null;

// Helper: Get exercise type (writing/speaking)
function getExerciseTypeFromQuestion() {
    // Try from currentContext first (most reliable)
    if (currentContext.type) {
        const typeCode = (currentContext.type || '').toUpperCase();
        if (typeCode.startsWith('W')) return 'writing';
        if (typeCode.startsWith('S')) return 'speaking';
    }
    
    // Try from question.exercises[0]
    const exercise = question?.exercises?.[0];
    if (exercise?.type) {
        const typeCode = (exercise.type.code || '').toUpperCase();
        if (typeCode.startsWith('W')) return 'writing';
        if (typeCode.startsWith('S')) return 'speaking';
    }
    
    // Try from navigation data
    if (navigationData && navigationData.length > 0) {
        const firstType = navigationData[0];
        if (firstType?.code) {
            const typeCode = (firstType.code || '').toUpperCase();
            if (typeCode.startsWith('W')) return 'writing';
            if (typeCode.startsWith('S')) return 'speaking';
        }
    }
    
    // Default fallback
    console.warn('Could not determine exercise type, defaulting to writing');
    return 'writing';
}

// Helper: Get reset mode flags
function getResetMode(lessonId) {
    const skipFlag = sessionStorage.getItem(`skipStatisticsCheck_${lessonId}`);
    const resetTimestamp = sessionStorage.getItem(`skipStatisticsCheck_${lessonId}_timestamp`);
    return {
        isActive: skipFlag === 'true' && resetTimestamp,
        timestamp: resetTimestamp ? parseInt(resetTimestamp) : null
    };
}

// Helper: Clear reset mode flags
function clearResetMode(lessonId) {
    sessionStorage.removeItem(`skipStatisticsCheck_${lessonId}`);
    sessionStorage.removeItem(`skipStatisticsCheck_${lessonId}_timestamp`);
}

// Helper: Set reset mode with timestamp
function setResetMode(lessonId) {
    const resetTimestamp = Date.now();
    sessionStorage.setItem(`skipStatisticsCheck_${lessonId}`, 'true');
    sessionStorage.setItem(`skipStatisticsCheck_${lessonId}_timestamp`, resetTimestamp.toString());
    return resetTimestamp;
}

// Helper: Show/hide answer wrappers based on exercise type
function toggleAnswerWrappers(exerciseType, show = true) {
    const writingWrapper = document.getElementById('writingAnswerWrapper');
    const speakingWrapper = document.getElementById('speakingAnswerWrapper');
    const bottomNav = document.querySelector('.bottom-navigation');
    
    console.log('toggleAnswerWrappers:', { exerciseType, show, writingWrapper: !!writingWrapper, speakingWrapper: !!speakingWrapper });
    
    if (show) {
        if (writingWrapper) {
            writingWrapper.style.display = exerciseType === 'writing' ? '' : 'none';
            console.log('Writing wrapper display:', writingWrapper.style.display);
        }
        if (speakingWrapper) {
            speakingWrapper.style.display = exerciseType === 'speaking' ? '' : 'none';
            console.log('Speaking wrapper display:', speakingWrapper.style.display);
        }
        if (bottomNav) bottomNav.style.display = '';
    } else {
        if (writingWrapper) writingWrapper.style.display = 'none';
        if (speakingWrapper) speakingWrapper.style.display = 'none';
        if (bottomNav) bottomNav.style.display = 'none';
    }
}

// Helper: Hide statistics section
function hideStatisticsSection() {
    const statisticsSection = document.getElementById('lessonStatisticsSection');
    if (statisticsSection) {
        statisticsSection.style.display = 'none';
    }
}

function showExerciseStatistics(stats) {
    const statisticsSection = document.getElementById('lessonStatisticsSection');
    const statisticsContent = document.getElementById('lessonStatisticsContent');
    
    if (!statisticsSection || !statisticsContent) {
        console.warn('Statistics section elements not found');
        return;
    }
    
    // Save exercise type before hiding wrappers
    savedExerciseType = getExerciseTypeFromQuestion();
    
    // Calculate percentage
    const percentage = stats.total_questions > 0 
        ? Math.round((stats.completed_questions / stats.total_questions) * 100) 
        : 0;
    
    statisticsContent.innerHTML = `
        <div class="statistics-header">
            <h3>🎉 Hoàn thành bài tập!</h3>
        </div>
        <div class="statistics-body">
            <div class="statistics-summary">
                <div class="stat-item">
                    <div class="stat-label">Điểm trung bình</div>
                    <div class="stat-value highlight">${stats.average_score}/10</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Câu đúng</div>
                    <div class="stat-value">${stats.correct_count}/${stats.completed_questions}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Hoàn thành</div>
                    <div class="stat-value">${stats.completed_questions}/${stats.total_questions} câu</div>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: ${percentage}%"></div>
            </div>
        </div>
        <div class="statistics-actions">
            <button id="reset-exercise-btn" class="btn-secondary" type="button">Làm lại bài tập</button>
        </div>
    `;
    
    if (statisticsSection) {
        statisticsSection.style.display = 'block';
        toggleAnswerWrappers(savedExerciseType, false);
    }
    
    // Reset button - reset exercise attempts
    const resetBtn = document.getElementById('reset-exercise-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', async () => {
            if (confirm('Bạn có muốn làm lại bài tập này từ đầu?')) {
                await resetExerciseAttempts(stats.exercise_id);
            }
        });
    }
}

async function resetExerciseAttempts(exerciseId) {
    try {
        const response = await questionApi.deleteExerciseAttempts(exerciseId, userId);
        
        if (response.status === 'success') {
            hideStatisticsSection();
            toggleAnswerWrappers(savedExerciseType || getExerciseTypeFromQuestion(), true);
            
            // Clear result displays
            if (resultCard) resultCard.style.display = 'none';
            if (resultPopup) resultPopup.classList.remove('active');
            if (answerField) answerField.value = '';
            clearStoredAnswersForExercise();
            resetAnswer();
            
            // Get lesson_id for reset mode
            const lessonId = currentContext.lesson_id || question?.exercises?.[0]?.lesson_id;
            if (lessonId) {
                setResetMode(lessonId);
            }
            
            // Navigate to first question of the exercise
            const exercise = question?.exercises?.[0];
            if (exercise && exercise.questions && exercise.questions.length > 0) {
                const firstQuestion = exercise.questions[0];
                const exerciseType = getExerciseTypeFromQuestion();
                const url = buildEmbedUrl(firstQuestion.id, exerciseId, exerciseType);
                window.location.href = url;
                return;
            }
            
            // Fallback: reload page
            window.location.reload();
        } else {
            alert('Có lỗi xảy ra khi reset bài tập. Vui lòng thử lại.');
        }
    } catch (error) {
        console.error('Error resetting exercise attempts:', error);
        alert('Có lỗi xảy ra khi reset bài tập. Vui lòng thử lại.');
    }
}

async function resetLessonAttempts(lessonId) {
    try {
        const response = await questionApi.deleteLessonAttempts(lessonId, userId);
        
        if (response.status === 'success') {
            hideStatisticsSection();
            toggleAnswerWrappers(savedExerciseType || getExerciseTypeFromQuestion(), true);
            
            // Clear result displays
            if (resultCard) resultCard.style.display = 'none';
            if (resultPopup) resultPopup.classList.remove('active');
            if (answerField) answerField.value = '';
            clearStoredAnswersForExercise();
            resetAnswer();
            
            // Set reset mode with timestamp
            setResetMode(lessonId);
            
            // Navigate to first question of the lesson
            // Get first question from navigation data
            if (window.appData && window.appData.navigation && Array.isArray(window.appData.navigation)) {
                const navigation = window.appData.navigation;
                // Find lesson in navigation data structure: types -> lessons -> exercises
                for (const typeData of navigation) {
                    if (typeData.lessons && Array.isArray(typeData.lessons)) {
                        const lesson = typeData.lessons.find(l => l.id === lessonId);
                        if (lesson && lesson.exercises && Array.isArray(lesson.exercises) && lesson.exercises.length > 0) {
                            const firstExercise = lesson.exercises[0];
                            if (firstExercise && firstExercise.first_question_id) {
                                // Navigate to first question of first exercise
                                const exerciseType = typeData.code?.startsWith('W') ? 'writing' : 'speaking';
                                const url = buildEmbedUrl(firstExercise.first_question_id, firstExercise.id, exerciseType);
                                window.location.href = url;
                                return;
                            }
                        }
                    }
                }
            }
            
            // Fallback: reload page but skip statistics check
            window.location.reload();
        } else {
            alert('Có lỗi xảy ra khi reset bài học. Vui lòng thử lại.');
        }
    } catch (error) {
        console.error('Error resetting lesson attempts:', error);
        alert('Có lỗi xảy ra khi reset bài học. Vui lòng thử lại.');
    }
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').style.display = 'block';
}

function updateQuestionCounters() {
    const hasQuestions = totalQuestions > 0;
    const displayIndex = hasQuestions ? currentQuestionIndex + 1 : null;

    if (questionCounter) {
        questionCounter.textContent = hasQuestions ? `(${displayIndex}/${totalQuestions})` : '(—)';
    }

    if (questionCounterBanner) {
        questionCounterBanner.textContent = hasQuestions
            ? `Câu ${displayIndex}/${totalQuestions}`
            : 'Câu —';
    }
}

function setNavigationBtnUrl() {
    if (!prevBtn || !nextBtn) return;

    // Calculate prev/next from exerciseQuestions array (more reliable)
    let prevQuestionId = null;
    let nextQuestionId = null;

    if (Array.isArray(exerciseQuestions) && exerciseQuestions.length > 0) {
        const currentIndex = exerciseQuestions.findIndex((q) => q.id === question.id);
        
        if (currentIndex > 0) {
            // Previous question exists
            prevQuestionId = exerciseQuestions[currentIndex - 1].id;
        }
        
        if (currentIndex >= 0 && currentIndex < exerciseQuestions.length - 1) {
            // Next question exists
            nextQuestionId = exerciseQuestions[currentIndex + 1].id;
        }
    }

    // Fallback to backend values if array calculation fails
    if (!prevQuestionId && question.prev_question_id) {
        prevQuestionId = question.prev_question_id;
    }
    if (!nextQuestionId && question.next_question_id) {
        nextQuestionId = question.next_question_id;
    }

    const hasPrev = Boolean(prevQuestionId);
    const hasNext = Boolean(nextQuestionId);

    // Check if current question is completed before enabling next
    const isCurrentCompleted = checkQuestionCompleted(question.id);
    const canGoNext = hasNext && isCurrentCompleted;

    prevBtn.disabled = !hasPrev;
    prevBtn.dataset.target = hasPrev ? prevQuestionId : '';
    
    nextBtn.disabled = !canGoNext;
    nextBtn.dataset.target = canGoNext ? nextQuestionId : '';
    
    // Add visual indicator if next is disabled due to incomplete
    if (nextBtn) {
        if (hasNext && !isCurrentCompleted) {
            nextBtn.title = 'Con hãy hoàn thành câu này trước khi chuyển sang câu tiếp theo nhé!';
        } else {
            nextBtn.title = '';
        }
    }

    updateQuestionCounters();

    if (questionSection) {
        questionSection.classList.toggle('nav-single', totalQuestions <= 1);
    }
}

// Cache for question completion status
const questionCompletionCache = new Map();

function checkQuestionCompleted(questionId) {
    if (!questionId) return false;
    
    // Check cache first
    if (questionCompletionCache.has(questionId)) {
        return questionCompletionCache.get(questionId);
    }
    
    // Check if question has attempt (from backend data)
    if (question.id === questionId && question.has_attempt) {
        questionCompletionCache.set(questionId, true);
        return true;
    }
    
    // Check from attempts count if available
    if (question.id === questionId && question.attempts_count > 0) {
        questionCompletionCache.set(questionId, true);
        return true;
    }
    
    // Default: assume not completed (will be updated when attempt is submitted)
    return false;
}

function markQuestionCompleted(questionId) {
    if (questionId) {
        questionCompletionCache.set(questionId, true);
        // Update navigation buttons after marking as completed
        setNavigationBtnUrl();
    }
}

function handleQuestionSelect(questionId) {
    if (!questionId) return;
    
    // Check if trying to go to next question without completing current
    const currentIndex = exerciseQuestions.findIndex((q) => q.id === question.id);
    const targetIndex = exerciseQuestions.findIndex((q) => q.id === questionId);
    
    if (targetIndex > currentIndex) {
        // Going forward - check if current is completed
        const isCurrentCompleted = checkQuestionCompleted(question.id);
        if (!isCurrentCompleted) {
            showError('Con hãy hoàn thành câu này trước khi chuyển sang câu tiếp theo nhé!');
            return;
        }
        
        // Check if all previous questions are completed
        for (let i = 0; i < targetIndex; i++) {
            const prevQuestionId = exerciseQuestions[i].id;
            if (!checkQuestionCompleted(prevQuestionId)) {
                showError('Con hãy hoàn thành tất cả các câu trước đó trước nhé!');
                return;
            }
        }
    }
    
    const url = buildEmbedUrl(questionId);
    window.location.assign(url);
}

function initEventListeners() {
    if (submitBtn) {
        submitBtn.addEventListener('click', () => submitAnswer());
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', resetAnswer);
    }

    // Instruction popup handlers
    function openInstructionPopup() {
        if (instructionPopup) {
            instructionPopup.classList.add('show');
        }
    }
    
    function closeInstructionPopup() {
        if (instructionPopup) {
            instructionPopup.classList.remove('show');
        }
    }
    
    if (instructionToggleBtn) {
        instructionToggleBtn.addEventListener('click', openInstructionPopup);
    }
    
    if (instructionPopupClose) {
        instructionPopupClose.addEventListener('click', closeInstructionPopup);
    }
    
    if (instructionPopup) {
        instructionPopup.addEventListener('click', (e) => {
            if (e.target === instructionPopup) {
                closeInstructionPopup();
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && instructionPopup.classList.contains('show')) {
                closeInstructionPopup();
            }
        });
    }
    
    // Close popup handlers
    if (resultPopupClose) {
        resultPopupClose.addEventListener('click', closeResultPopup);
    }

    if (resultPopup) {
        resultPopup.addEventListener('click', (e) => {
            if (e.target === resultPopup) {
                closeResultPopup();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && resultPopup.classList.contains('show')) {
                closeResultPopup();
            }
        });
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

    // Enter key to submit (for writing exercises)
    const exerciseTypeCode = question?.exercises?.[0]?.type?.code || currentContext.type;
    if (answerField && exerciseTypeCode?.includes('W')) {
        answerField.addEventListener('keydown', (e) => {
            // Submit on Enter, but allow Shift+Enter for new line
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                // Only submit if button is not disabled
                if (submitBtn && !submitBtn.disabled) {
                    submitAnswer();
                }
            }
        });
    }

    // Enter key for WCS suffix field
    if (answerSuffixEl && exerciseTypeCode === 'WCS') {
        answerSuffixEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (submitBtn && !submitBtn.disabled) {
                    submitAnswer();
                }
            }
        });
    }
}

async function initQuestionPage() {
    hydrateQuestionContent();
    hydrateStoredAnswer();
    initTranslationFeature();
    initEventListeners();
    
    // Initialize question completion status from backend
    if (question.has_attempt) {
        markQuestionCompleted(question.id);
    }
    
    setNavigationBtnUrl();
    if (sampleAudioBtn)  setSampleAudio();
    initNavigationSelectors();
    
    // Ensure answer wrapper is visible on page load
    const exerciseType = getExerciseTypeFromQuestion();
    toggleAnswerWrappers(exerciseType, true);
    
    // Check and show exercise statistics on page load if already completed
    await checkAndShowExerciseStatistics();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuestionPage);
} else {
    initQuestionPage();
}

