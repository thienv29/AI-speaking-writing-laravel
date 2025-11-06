import lessonApi from "../api/lessonApi.js";

const lessonId = window.appData.lessonId;

const exerciseList = document.getElementById('exerciseList');

let lesson = null;

async function initLessonPage() {
    try {
        const res = await lessonApi.getById(lessonId);
        lesson = res.data;
        console.log(lesson);
        renderExercises(lesson);
        renderLessonInfo(lesson);
    } catch (error) {
        console.error('Lỗi khi tải bài học:', error);
    }
}

await initLessonPage();

// Mở/đóng dropdown exercise
function toggleExercise(id) {
  const content = document.getElementById(`exercise-${id}`);
  const arrow = document.getElementById(`arrow-${id}`);
  const isOpen = content.classList.contains('max-h-[1000px]');

  document.querySelectorAll('[id^="exercise-"]').forEach(div => {
    div.classList.remove('max-h-[1000px]');
    div.classList.add('max-h-0');
  });
  document.querySelectorAll('[id^="arrow-"]').forEach(svg => {
    svg.classList.remove('rotate-180');
  });

  if (!isOpen) {
    content.classList.remove('max-h-0');
    content.classList.add('max-h-[1000px]');
    arrow.classList.add('rotate-180');
  }
}

//Render thông tin Lesson
function renderLessonInfo(lesson){
    document.getElementById('lesson-img').src = lesson.img_url ||
    'https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg';
    document.getElementById('lesson-title').innerText = lesson.title;
    document.getElementById('lesson-desc').innerText = lesson.description;
    document.getElementById('lesson-difficulty').innerText = lesson.level;
}

//Render danh sách Exercises
function renderExercises(lesson) {
  lesson.exercises.forEach(ex => {
    const wrapper = document.createElement('div');
    wrapper.className = 'bg-white shadow rounded-lg overflow-hidden';

    wrapper.innerHTML = `
      <button class="w-full text-left px-6 py-4 bg-[#f3e6ff] font-semibold text-gray-800 flex justify-between items-center"
      onclick="toggleExercise(${ex.id})">
        ${ex.title}
        <svg id="arrow-${ex.id}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div id="exercise-${ex.id}" class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-white">
        <div class="p-6 space-y-4">
          ${ex.questions.map(q => `
            <div class="border border-gray-200 rounded-lg p-4">
              <p class="text-gray-800 font-medium">${q.prompt_text}</p>
            </div>
          `).join('')}
        </div>
      </div>
    `;
    exerciseList.appendChild(wrapper);
  });
}

window.toggleExercise = toggleExercise;

function startLesson() {
    const firstQuestion = lesson.exercises[0].questions[0];
    window.location.href = `/questions/${firstQuestion.id}`;
}

document.getElementById('start-lesson-btn').addEventListener('click', startLesson);