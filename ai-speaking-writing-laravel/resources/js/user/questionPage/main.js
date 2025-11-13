//import './utils/translationBySelectText'
import { isEmpty } from 'lodash';
import questionApi from '../../api/questionApi';
import {initTranslationFeature} from './translationBySelectText'
import Feedback from './writing-feedback';

// Current question id
const question = window.appData.question;
const userId = "2";

const prevBtn = document.getElementById('prevQuestionBtn');
const nextBtn = document.getElementById('nextQuestionBtn');
const submitBtn = document.getElementById('submitBtn');
const resetBtn = document.getElementById('resetBtn');
const questionSelect = document.getElementById('questionSelect');

const loadingEl = document.getElementById('loading');
const errorEl = document.getElementById('error');

const resultCard = document.getElementById('resultCard');
const answerField = document.getElementById('userAnswer');
const answerSuffixEl = document.getElementById('answerSuffix');
const resultFeedback = document.getElementById('resultFeedback');

const recordBtn = document.getElementById('mic-btn');
const sampleAudioBtn = document.getElementById('sample-audio-btn');
const recordingIndicator = document.getElementById('recording-indicator');
const userAudio = document.getElementById('user-audio');

const SOUND_BASE_PATH = '/assets/sounds';
const SOUND_MAP = {
    button: `${SOUND_BASE_PATH}/click-soft.mp3`,
    success: `${SOUND_BASE_PATH}/success.mp3`,
    failure: `${SOUND_BASE_PATH}/try-again.mp3`,
};

const audioCache = {};

let sampleAudio = null;

const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = null, userAnswer = '';
let mediaRecorder = null, mediaStream = null, chunks = [];
let mediaSupported = false;
let isRecording = false;

async function setSampleAudio() {
    if (question.audio_url) {
        document.getElementById("sample-audio-btn").src = question.audio_url;
    } else {
        try {
            const audioUrl = question.audio_url || 
            await questionApi.textToSpeech(question.prompt_text, "en");

            if (sampleAudio && sampleAudio.src.startsWith("blob:")) {
                sampleAudio.pause();
                URL.revokeObjectURL(sampleAudio.src);
            }

            sampleAudio = new Audio(audioUrl);

            sampleAudioBtn.onclick = () => {
                sampleAudio.play();
            };

            console.log("Audio TTS đã sẵn sàng:", audioUrl);
        } catch (err) {
            console.error("Không tạo được audio sample:", err);
        }
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
    recordingIndicator.classList.remove('hidden');
    recordingIndicator.style.display='flex';
    userAudio.classList.add('hidden');
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

        submitBtn.disabled = true;
        loadingEl.style.display = 'flex';
        errorEl.style.display = 'none';
    } else {
        if (!userAnswer || userAnswer.trim() === '') {
            showError('Vui lòng đọc nội dung.');
            recordingIndicator.style.display='none';
            return;
        }

        console.log("User answer: " + userAnswer);

        userAudio.src = URL.createObjectURL(audioBlob);
        userAudio.classList.remove('hidden');
    }

    playUiSound('button');

    resultCard.style.display = 'none';

    try {
        const response = await questionApi.evaluateAnswer(userId, question.id, userAnswer, audioBlob);

        renderResult(response.data);
    } catch (error) {
        showError(error.message);
    } finally {
        if(submitBtn) submitBtn.disabled = false;
        loadingEl.style.display = 'none';
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
            answerField.value = question.stater_text ? `${question.starter_text.trim()} ` : '';
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
    if (recordingIndicator) recordingIndicator.style.display='none';

    const stage = data.is_correct ? 'success' : 'failure';
    const statusText = data.is_correct ? 'Làm tốt lắm!' : 'Cùng thử lại nhé';
    const statusClass = data.is_correct ? 'success' : 'failure';
    const highlightHtml = Feedback.renderHighlights ? Feedback.renderHighlights(data) : '';
    const notesHtml = Feedback.renderNotes ? Feedback.renderNotes(data) : '';

    let feedbackText = null;
    let score = null;
    if(question.exercise.type.code.includes('W')) {
        score = data.score ? `${data.score}/100` : (data.feedback.includes('Điểm:') ? data.feedback.split('Điểm:')[1].split('/')[0] + '/100' : 'N/A');
        feedbackText = Feedback.formatFeedbackForKids ? Feedback.formatFeedbackForKids(data.feedback) : (data.feedback || '');
    } else {
        feedbackText = data.feedback;
        if(data.is_correct) {
            score = "100/100";
        } else {
            score = "0/100";
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
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('error').style.display = 'block';
}

function setNavigationBtnUrl() {
    if (!question.prev_question_id) {
        prevBtn.classList.add("opacity-0", "pointer-events-none");
    } else {
        prevBtn.href = `/questions/${question.prev_question_id}`;
        prevBtn.classList.remove("opacity-0", "pointer-events-none");
    }

    if (question.next_question_id) {
        nextBtn.href = `/questions/${question.next_question_id}`;
        nextBtn.classList.remove("opacity-0", "pointer-events-none");
    } else {
        nextBtn.classList.add("opacity-0", "pointer-events-none");
    }
}

function handleQuestionSelect(questionId) {
    window.location.assign('/questions/' + questionId);
}

function initEventListeners() {
    if (submitBtn) {
        submitBtn.addEventListener('click', submitAnswer);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', resetAnswer);
    }

    if (questionSelect) {
        questionSelect.addEventListener('change', (e) => {
            handleQuestionSelect(e.target.value);
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
    initTranslationFeature();
    initEventListeners();
    setNavigationBtnUrl();
    if (sampleAudioBtn)  setSampleAudio(question);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuestionPage);
} else {
    initQuestionPage();
}

