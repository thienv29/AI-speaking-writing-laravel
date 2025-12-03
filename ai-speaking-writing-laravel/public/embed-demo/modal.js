const exerciseInput = document.getElementById('exerciseId');
const questionInput = document.getElementById('questionId');
const loadBtn = document.getElementById('loadBtn');
const openModalBtn = document.getElementById('openModalBtn');
const iframe = document.getElementById('exerciseFrame');
const modal = document.getElementById('embedModal');
const modalBackdrop = document.getElementById('modalBackdrop');
const closeModalBtn = document.getElementById('closeModalBtn');
const modalFrame = document.getElementById('modalFrame');

const BASE_URL = 'http://localhost:8000';

// Build URL using new route format: /embed-writing/exercises/{exercise} or /embed-speaking/exercises/{exercise}
// For demo, we'll use writing route by default. You can change 'writing' to 'speaking' if needed.
function buildUrl(exerciseId, questionId = null) {
    const exercise = Number(exerciseId);
    const question = questionId ? Number(questionId) : null;
    
    if (!Number.isFinite(exercise) || exercise <= 0) {
        console.warn('Invalid exercise ID, using default: 1');
        return `${BASE_URL}/embed-writing/exercises/1`;
    }
    
    // Use writing route by default. Change to 'embed-speaking' for speaking exercises
    const url = `${BASE_URL}/embed-writing/exercises/${exercise}`;
    return question && Number.isFinite(question) && question > 0 
        ? `${url}?questionId=${question}` 
        : url;
}

function loadExercise() {
    const exerciseId = exerciseInput.value || exerciseInput.placeholder || '1';
    const questionId = questionInput.value || null;
    iframe.src = buildUrl(exerciseId, questionId);
}

function openModal() {
    const exerciseId = exerciseInput.value || exerciseInput.placeholder || '1';
    const questionId = questionInput.value || null;
    modalFrame.src = buildUrl(exerciseId, questionId);
    modal.classList.add('show');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    modalFrame.src = 'about:blank';
}

loadBtn?.addEventListener('click', loadExercise);
openModalBtn?.addEventListener('click', openModal);
closeModalBtn?.addEventListener('click', closeModal);
modalBackdrop?.addEventListener('click', closeModal);

// Load on Enter key for both inputs
[exerciseInput, questionInput].forEach(input => {
    input?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            loadExercise();
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal.classList.contains('show')) {
        closeModal();
    }
});

// Load mặc định khi mở trang
loadExercise();
