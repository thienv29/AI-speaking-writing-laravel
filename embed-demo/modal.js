const questionInput = document.getElementById('questionId');
const loadBtn = document.getElementById('loadBtn');
const openModalBtn = document.getElementById('openModalBtn');
const iframe = document.getElementById('exerciseFrame');
const modal = document.getElementById('embedModal');
const modalBackdrop = document.getElementById('modalBackdrop');
const closeModalBtn = document.getElementById('closeModalBtn');
const modalFrame = document.getElementById('modalFrame');

const BASE_URL = 'http://localhost:8000/embed/question/';

function buildUrl(questionId) {
    const id = Number(questionId);
    return `${BASE_URL}${Number.isFinite(id) && id > 0 ? id : 1}`;
}

function loadExercise() {
    iframe.src = buildUrl(questionInput.value);
}

function openModal() {
    modalFrame.src = buildUrl(questionInput.value);
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

questionInput?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        loadExercise();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal.classList.contains('show')) {
        closeModal();
    }
});

// Load mặc định khi mở trang
loadExercise();
