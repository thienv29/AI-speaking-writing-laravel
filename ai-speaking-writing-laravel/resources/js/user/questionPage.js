import questionApi from "../api/questionApi.js";

const questionId = window.appData.questionId;

const recordBtn = document.getElementById('mic-btn');
const answerBox = document.getElementById('answer-box');
const answerText = document.getElementById('answer-text');
const sampleAudioBtn = document.getElementById('sample-audio-btn');
const prevBtn = document.getElementById('prev-question-btn');
const nextBtn = document.getElementById('next-question-btn');
const recordingIndicator = document.getElementById('recording-indicator');
const userAudio = document.getElementById('user-audio');

let sampleAudio = null;

const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = null, userAnswer = '';
let mediaRecorder = null, mediaStream = null, chunks = [];
let mediaSupported = false;
let isRecording = false;

recordBtn.addEventListener('click', () => {
    if (isRecording) {
        stopRec();
    } else {
        startRec();
    }
});

async function setSampleAudio(question) {
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
    userAudio.classList.add('hidden');

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

async function checkResult(questionId, userAnswer, audioBlob = null) {
    try {
        const res = await questionApi.checkResult(questionId, userAnswer, audioBlob);
        console.log(res);
        return res;
    } catch (err) {
        console.error("Lỗi khi kiểm tra kết quả:", err);
    }
}

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

    try {
        if (!userAnswer || userAnswer.trim() === '') {
            renderResult(false);
            return;
        }
        console.log(userAnswer);
        console.log("questionId: " + questionId);

        const res = await checkResult(questionId, userAnswer, audioBlob);
        
        renderResult(res.is_correct);

        userAudio.src = URL.createObjectURL(audioBlob);
        userAudio.classList.remove('hidden');
    } catch (err) {
        console.error("Lỗi khi kiểm tra kết quả:", err);
    }
}

function renderResult(is_correct, empty=false) {
    recordingIndicator.classList.add('hidden');

    const audioRight = new Audio('/sounds/right-answer.mp3');
    const audioWrong = new Audio('/sounds/wrong-answer.mp3');

    if (empty) {
        answerText.textContent = '❌ Không nhận diện được câu trả lời!';
        answerText.className = 'w-full text-center px-6 py-3 rounded-xl text-white font-semibold text-lg bg-yellow-500';
        return;
    }

    if (is_correct) {
        audioRight.play();
        answerText.textContent = '🎉 Chính xác!';
        answerText.className = 'w-full text-center px-6 py-3 rounded-xl text-white font-semibold text-lg bg-green-500';
        confetti({
        particleCount: 120,
        spread: 80,
        origin: { y: 0.7 }
        });
    } else {
        audioWrong.play();
        answerText.textContent = '❌ Sai rồi!';
        answerText.className = 'w-full text-center px-6 py-3 rounded-xl text-white font-semibold text-lg bg-red-500';
    }

    answerBox.classList.remove('hidden');
    answerBox.classList.add('flex');
}

function setNavigationBtnUrl(question) {
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

async function initQuestionPage() {
  try {
    const question = await questionApi.getById(questionId);
    renderQuestion(question.data);
    setSampleAudio(question.data);
    setNavigationBtnUrl(question.data);
  } catch (err) {
    console.error("Lỗi khi tải câu hỏi:", err);
  }
}

function renderQuestion(question) {
    console.log(question);
    
    document.getElementById("exercise-title").textContent = `${question.exercise.title}`+": " + `${question.exercise.instruction}`; 
    document.getElementById("question-counter").textContent = `${question.order_index}/${question.exercise.questions_count}`;
    document.getElementById("question-img").src = question.img_url 
    || 'https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg';
    document.getElementById("question-prompt-text").textContent = question.prompt_text;
    document.getElementById("sample-audio-btn").src = question.audio_url;
}

await initQuestionPage();
