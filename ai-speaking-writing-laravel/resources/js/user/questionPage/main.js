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

// Get all unique lessons from all types
function getAllLessons() {
    const lessonsMap = new Map();
    
    // Collect all lessons from all types
    for (const type of navigationData) {
        if (!Array.isArray(type.lessons)) continue;
        for (const lesson of type.lessons) {
            const lessonId = Number(lesson.id);
            if (!lessonsMap.has(lessonId)) {
                lessonsMap.set(lessonId, {
                    id: lesson.id,
                    title: lesson.title,
                });
            }
        }
    }
    
    return Array.from(lessonsMap.values());
}

// Check if a lesson has any types (has exercises with questions)
function lessonHasTypes(lessonId) {
    if (!lessonId) return false;
    
    for (const type of navigationData) {
        const lessonData = type.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
        if (lessonData && Array.isArray(lessonData.exercises) && lessonData.exercises.length > 0) {
            // Check if lesson has at least one exercise with questions
            const hasQuestions = lessonData.exercises.some((exercise) => {
                return getFirstQuestionIdFromExercise(exercise) !== null;
            });
            if (hasQuestions) {
                return true;
            }
        }
    }
    
    return false;
}

// Get available types for a lesson (filter types that have exercises in this lesson)
function getAvailableTypesForLesson(lessonId) {
    if (!lessonId) return [];
    
    const availableTypes = [];
    
    // Loop through all types to find which ones have this lesson
    for (const type of navigationData) {
        const lessonData = type.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
        if (lessonData && Array.isArray(lessonData.exercises) && lessonData.exercises.length > 0) {
            // Check if lesson has at least one exercise with questions
            const hasQuestions = lessonData.exercises.some((exercise) => {
                return getFirstQuestionIdFromExercise(exercise) !== null;
            });
            
            if (hasQuestions) {
                availableTypes.push(type);
            }
        }
    }
    
    return availableTypes;
}

// Get available lessons for a type
function getAvailableLessonsForType(typeCode) {
    if (!typeCode) return [];
    
    const typeData = navigationData.find((type) => type.code === typeCode);
    if (!typeData) return [];
    
    return Array.isArray(typeData.lessons) ? typeData.lessons : [];
}

function renderTypeOptions(lessonId = null) {
    if (!typeSelect) return;
    
    // LUÔN LUÔN hiển thị tất cả types (không filter)
    // Nếu có lessonId, highlight types có trong lesson đó
    const allTypes = navigationData;
    const availableTypesForLesson = lessonId ? getAvailableTypesForLesson(lessonId) : [];
    
    if (!allTypes.length) {
        typeSelect.innerHTML = '<option value="">Không có dạng bài</option>';
        typeSelect.disabled = true;
        return;
    }

    // Hiển thị tất cả types, nhưng highlight types có trong lesson hiện tại
    const optionsHtml = allTypes.map((type) => {
        const label = type.name || type.code;
        const selected = type.code === activeTypeCode ? 'selected' : '';
        // Nếu có lessonId và type không có trong lesson, vẫn hiển thị nhưng có thể style khác
        const isAvailable = !lessonId || availableTypesForLesson.some((t) => t.code === type.code);
        return `<option value="${type.code}" ${selected} ${!isAvailable ? 'style="color: #999;"' : ''}>${label}</option>`;
    }).join('');

    typeSelect.innerHTML = optionsHtml;
    
    // Nếu có lessonId và lesson chỉ có 1 type hoặc không có type → lock type selector
    if (lessonId) {
        if (availableTypesForLesson.length <= 1) {
            // Lesson chỉ có 1 type hoặc không có type → lock type selector
            typeSelect.disabled = true;
        } else {
            // Lesson có nhiều hơn 1 type → unlock type selector
            typeSelect.disabled = false;
        }
    } else {
        // Không có lessonId → unlock type selector
        typeSelect.disabled = false;
    }
}

function updateLessonOptions(typeCode = null) {
    if (!lessonSelect) return;
    
    // LUÔN LUÔN hiển thị tất cả lessons (không filter theo type)
    const allLessons = getAllLessons();
    
    if (!allLessons.length) {
        lessonSelect.innerHTML = '<option value="">Không có bài học</option>';
        lessonSelect.disabled = true;
        activeLessonId = null;
        return;
    }

    // Nếu có typeCode, lấy danh sách lessons có trong type đó để highlight
    const lessonsInType = typeCode ? getAvailableLessonsForType(typeCode).map((l) => Number(l.id)) : [];

    // Hiển thị tất cả lessons, nhưng disable lesson nếu lesson đó không có type nào
    const optionsHtml = allLessons.map((lesson) => {
        const lessonId = Number(lesson.id);
        const selected = lessonId === Number(activeLessonId) ? 'selected' : '';
        const hasTypes = lessonHasTypes(lessonId);
        const disabled = !hasTypes ? 'disabled' : '';
        const style = !hasTypes ? 'style="color: #999;"' : '';
        // Nếu có typeCode và lesson không có trong type đó, vẫn hiển thị nhưng có thể style khác
        const isInType = !typeCode || lessonsInType.includes(lessonId);
        return `<option value="${lesson.id}" ${selected} ${disabled} ${style}>${lesson.title}</option>`;
    }).join('');

    lessonSelect.innerHTML = optionsHtml;
    // KHÔNG disable lesson selector (chỉ disable từng option)
    lessonSelect.disabled = false;
    
    // Nếu current lesson không có types, không tự động chọn lesson khác (giữ nguyên selection)
    // Chỉ đảm bảo lesson hiện tại có trong list
    if (activeLessonId && !allLessons.some((lesson) => Number(lesson.id) === activeLessonId)) {
        // Nếu lesson hiện tại không có trong list, tìm lesson đầu tiên có types
        const firstAvailableLesson = allLessons.find((lesson) => lessonHasTypes(Number(lesson.id)));
        if (firstAvailableLesson) {
            activeLessonId = Number(firstAvailableLesson.id);
            lessonSelect.value = String(activeLessonId);
        }
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

// Get first question ID from a lesson (loops through all exercises in lesson)
function getFirstQuestionIdFromLesson(typeCode, lessonId) {
    if (!typeCode || !lessonId) return null;

    const typeData = navigationData.find((type) => type.code === typeCode);
    if (!typeData) return null;

    const lessonData = typeData.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
    if (!lessonData) return null;

    const exercises = Array.isArray(lessonData.exercises) ? lessonData.exercises : [];
    
    // Loop through exercises to find first question
    for (const exercise of exercises) {
        const questionId = getFirstQuestionIdFromExercise(exercise);
        if (questionId) {
            return Number(questionId);
        }
    }

    return null;
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

    // Initialize: hiển thị tất cả lessons và types
    updateLessonOptions(activeTypeCode); // Hiển thị tất cả lessons, nhưng có thể highlight theo type
    renderTypeOptions(activeLessonId); // Hiển thị tất cả types, lock/unlock dựa trên số lượng types trong lesson

    // Đảm bảo activeLessonId và activeTypeCode có thể kết hợp được
    if (activeLessonId && activeTypeCode) {
        const availableTypes = getAvailableTypesForLesson(activeLessonId);
        const availableLessons = getAvailableLessonsForType(activeTypeCode);
        
        // Nếu lesson hiện tại không có trong type hiện tại, tìm lesson đầu tiên có types trong type này
        if (availableLessons.length > 0 && !availableLessons.some((l) => Number(l.id) === activeLessonId)) {
            const firstLessonWithTypes = availableLessons.find((lesson) => lessonHasTypes(Number(lesson.id)));
            if (firstLessonWithTypes) {
                activeLessonId = Number(firstLessonWithTypes.id);
                if (lessonSelect) {
                    lessonSelect.value = String(activeLessonId);
                }
                // Update type options sau khi thay đổi lesson
                renderTypeOptions(activeLessonId);
            }
        }
        
        // Nếu type hiện tại không có trong lesson hiện tại, switch to first available type
        if (availableTypes.length > 0 && !availableTypes.some((t) => t.code === activeTypeCode)) {
            activeTypeCode = availableTypes[0].code;
            if (typeSelect) {
                typeSelect.value = activeTypeCode;
            }
        }
    } else if (activeLessonId) {
        // Có lesson nhưng không có type, tìm type đầu tiên có trong lesson
        const availableTypes = getAvailableTypesForLesson(activeLessonId);
        if (availableTypes.length > 0) {
            activeTypeCode = availableTypes[0].code;
            if (typeSelect) {
                typeSelect.value = activeTypeCode;
            }
        }
        // Update type options sau khi cập nhật type
        renderTypeOptions(activeLessonId);
    } else if (activeTypeCode) {
        // Có type nhưng không có lesson, tìm lesson đầu tiên có trong type
        const availableLessons = getAvailableLessonsForType(activeTypeCode);
        const firstLessonWithTypes = availableLessons.find((lesson) => lessonHasTypes(Number(lesson.id)));
        if (firstLessonWithTypes) {
            activeLessonId = Number(firstLessonWithTypes.id);
            if (lessonSelect) {
                lessonSelect.value = String(activeLessonId);
            }
        }
        // Update lesson options sau khi cập nhật lesson
        updateLessonOptions(activeTypeCode);
        renderTypeOptions(activeLessonId);
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', (e) => {
            const selectedType = e.target.value;
            if (!selectedType || selectedType === activeTypeCode) return;

            activeTypeCode = selectedType;
            activeExerciseId = null;

            // Update lessons: hiển thị tất cả lessons (nhưng có thể highlight theo type)
            updateLessonOptions(activeTypeCode);
            
            // Tìm question từ type và lesson hiện tại
            // Nếu lesson hiện tại có trong type mới, dùng lesson hiện tại
            const availableLessons = getAvailableLessonsForType(activeTypeCode);
            const currentLessonInType = availableLessons.find((lesson) => Number(lesson.id) === activeLessonId);
            
            if (currentLessonInType && lessonHasTypes(activeLessonId)) {
                // Lesson hiện tại có trong type mới và có types
                // Update type options (sẽ lock/unlock dựa trên số lượng types trong lesson)
                renderTypeOptions(activeLessonId);
                
                const questionId = getFirstQuestionIdFromLesson(activeTypeCode, activeLessonId);
                if (questionId) {
                    window.location.assign(`/embed/question/${questionId}`);
                }
            } else {
                // Lesson hiện tại không có trong type mới hoặc không có types
                // Tìm lesson đầu tiên có types trong type mới
                const firstLessonWithTypes = availableLessons.find((lesson) => lessonHasTypes(Number(lesson.id)));
                
                if (firstLessonWithTypes) {
                    activeLessonId = Number(firstLessonWithTypes.id);
                    if (lessonSelect) {
                        lessonSelect.value = String(activeLessonId);
                    }
                    
                    // Update type options sau khi thay đổi lesson (sẽ lock/unlock dựa trên số lượng types)
                    renderTypeOptions(activeLessonId);
                    
                    const questionId = getFirstQuestionIdFromLesson(activeTypeCode, activeLessonId);
                    if (questionId) {
                        window.location.assign(`/embed/question/${questionId}`);
                    }
                } else {
                    // Nếu type không có lesson nào, tìm question đầu tiên trong type
                    // Type selector sẽ không bị lock (vì không có lesson nào)
                    renderTypeOptions(null);
                    
                    const questionId = getFirstQuestionIdForType(activeTypeCode);
                    if (questionId) {
                        window.location.assign(`/embed/question/${questionId}`);
                    }
                }
            }
        });
    }

    if (lessonSelect) {
        lessonSelect.addEventListener('change', (e) => {
            const selectedLessonId = toNumber(e.target.value);
            if (!selectedLessonId || selectedLessonId === activeLessonId) return;
            
            // Check if lesson has types
            if (!lessonHasTypes(selectedLessonId)) {
                console.warn('Lesson does not have any types');
                // Vẫn cho phép chọn lesson này, nhưng sẽ disable type selector
                activeLessonId = selectedLessonId;
                renderTypeOptions(activeLessonId);
                return;
            }

            activeLessonId = selectedLessonId;
            activeExerciseId = null;

            // Filter types theo lesson (nhưng vẫn hiển thị tất cả)
            // renderTypeOptions sẽ tự động lock/unlock type selector dựa trên số lượng types
            renderTypeOptions(activeLessonId);
            
            // Đảm bảo type hiện tại có trong lesson
            const availableTypes = getAvailableTypesForLesson(activeLessonId);
            if (availableTypes.length > 0) {
                if (!availableTypes.some((type) => type.code === activeTypeCode)) {
                    // Current type not in lesson, switch to first available type
                    activeTypeCode = availableTypes[0].code;
                    if (typeSelect) {
                        typeSelect.value = activeTypeCode;
                    }
                }
            } else {
                // Lesson không có type nào, giữ nguyên type hiện tại (nhưng type selector sẽ bị lock)
                console.warn('Lesson does not have any available types');
            }
            
            const questionId = getFirstQuestionIdFromLesson(activeTypeCode, activeLessonId);

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

    // Đảm bảo có userAnswer trước khi submit
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
            userAnswer: userAnswer,
            savedUserAnswer: savedUserAnswer,
            audioBlobSize: audioBlob?.size || 0
        });
        if (errorEl) {
            showError('Vui lòng đọc nội dung rõ ràng hơn.');
        }
        if (recordingIndicator) {
            recordingIndicator.style.display = 'none';
        }
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
    const exerciseTypeCode = (question?.exercise?.type?.code || '').toString().toUpperCase();
    const isWcs = exerciseTypeCode === 'WCS';
    const isWritingType = exerciseTypeCode.startsWith('W');
    
    if (!isWritingType) {
        // Not a writing exercise, skip validation
        return;
    }
    
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
    // Xác định loại exercise
    const exerciseTypeCode = (question?.exercise?.type?.code || '').toString().toUpperCase();
    const isWritingType = exerciseTypeCode.startsWith('W');
    const isSpeakingType = exerciseTypeCode.startsWith('S');
    
    // Dùng tham số answerText, nếu rỗng thì dùng biến global userAnswer (cho speaking)
    let finalAnswer = answerText || userAnswer;
    
    if (isWritingType) {
        // Writing exercise
        validateAnswer(finalAnswer);
        finalAnswer = answerField?.value || finalAnswer;

        console.log("User answer (Writing): " + finalAnswer);

        if (isEmpty(finalAnswer)) {
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
    } else if (isSpeakingType) {
        // Speaking exercise (SPS, SPW)
        console.log("Speaking exercise detected, type:", exerciseTypeCode);
        console.log("finalAnswer before check:", finalAnswer);
        console.log("audioBlob:", audioBlob ? `size: ${audioBlob.size}` : 'null');
        
        if (!finalAnswer || finalAnswer.trim() === '') {
            console.error("No userAnswer found for speaking exercise");
            showError('Vui lòng đọc nội dung.');
            if (recordingIndicator) recordingIndicator.style.display = 'none';
            return;
        }

        if (!audioBlob) {
            console.error("No audioBlob found for speaking exercise");
            showError('Không có file ghi âm. Vui lòng ghi âm lại.');
            return;
        }

        console.log("User answer (Speaking): " + finalAnswer);
        console.log("Audio blob size:", audioBlob.size, "type:", audioBlob.type);

        // Đảm bảo userAudio đã được set trong stopRec()
        if (userAudio && !userAudio.src) {
            userAudio.src = URL.createObjectURL(audioBlob);
        }
        if (userAudio) {
            userAudio.style.display = 'block';
        }
        
        // Set loading state for speaking
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
        console.error("Unknown exercise type:", exerciseTypeCode);
        showError('Không xác định được loại bài tập.');
        return;
    }

    playUiSound('button');

    if (resultCard) resultCard.style.display = 'none';

    try {
        const response = await questionApi.evaluateAnswer(userId, question.id, finalAnswer, audioBlob);

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
    const exerciseTypeCode = (question?.exercise?.type?.code || '').toString().toUpperCase();
    const isWcs = exerciseTypeCode === 'WCS';
    const isWritingType = exerciseTypeCode.startsWith('W');
    const isSpeakingType = exerciseTypeCode.startsWith('S');
    
    if (isWritingType) {
        if (isWcs) {
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
    } else if (isSpeakingType) {
        // Reset speaking: clear audio and userAnswer
        if (userAudio) {
            userAudio.style.display = 'none';
            userAudio.src = '';
        }
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

    // Xác định loại exercise
    const exerciseTypeCode = (question?.exercise?.type?.code || '').toString().toUpperCase();
    const isWritingType = exerciseTypeCode.startsWith('W');
    const isSpeakingType = exerciseTypeCode.startsWith('S');
    
    const userWrittenAnswer = answerField?.value ?? '';
    let rawFeedback = data.feedback || '';

    if (isWritingType) {
        rawFeedback = sanitizeFeedbackMessage(rawFeedback, userWrittenAnswer);
        numericScore = Number.isFinite(data.score) ? Number(data.score) : extractScoreFromFeedback(rawFeedback);
        score = Number.isFinite(numericScore) ? `${numericScore}/100` : 'N/A';
        feedbackText = Feedback.formatFeedbackForKids ? Feedback.formatFeedbackForKids(rawFeedback) : rawFeedback;
    } else if (isSpeakingType) {
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

    // Enter key to submit (for writing exercises)
    if (answerField && question?.exercise?.type?.code?.includes('W')) {
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
    if (answerSuffixEl && question?.exercise?.type?.code === 'WCS') {
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

