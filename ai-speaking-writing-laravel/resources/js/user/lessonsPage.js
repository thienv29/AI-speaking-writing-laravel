import lessonApi from "../api/lessonApi.js";

const lessonList = document.getElementById('lessonList');
const searchInput = document.getElementById('searchInput');
const filterSelect = document.getElementById('filterSelect');
const sortSelect = document.getElementById('sortSelect');

let lessons = [];

async function initLessonsPage() {
  try {
    lessons = await lessonApi.getAll();
    console.log(lessons);
    
    renderLessons(lessons);
  } catch (err) {
    console.error("Lỗi khi tải Lessons:", err);
  }
}

await initLessonsPage();

function difficultyColor(level) {
  switch (level) {
    case 'Dễ': return 'bg-green-100 text-green-600';
    case 'Trung bình': return 'bg-yellow-100 text-yellow-600';
    case 'Khó': return 'bg-red-100 text-red-600';
    default: return 'bg-gray-100 text-gray-600';
  }
}

function renderLessons(lessons) {
  console.log(lessons);
  
  lessonList.innerHTML = '';
  lessons.forEach(lesson => {
    const card = document.createElement('div');
    card.className = 'bg-[#ffe7ea] rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col';

    card.innerHTML = `
      <div class="p-5 flex flex-col justify-between flex-1">
        <img src="${lesson.img_url 
        || 'https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg'}" 
        alt="${lesson.title}" class="h-40 w-full object-cover">
        <div>
          <h3 class="text-lg font-bold text-gray-800 mb-2">${lesson.title}</h3>
          <span class="inline-block px-3 py-1 text-sm font-bold rounded-full ${difficultyColor(lesson.level)}">
            ${lesson.level}
          </span>
        </div>
        <button
          class="mt-4 bg-[#ffe7ea] border-2 border-[#f6a914] text-[#f6a914] hover:text-white hover:bg-[#f6a914] font-medium px-4 py-2 rounded-full transition"
          onclick="window.location.href='/lessons/${lesson.id}'">
          Xem
        </button>
      </div>
    `;
    lessonList.appendChild(card);
  });
}

function applyFilters() {
  const search = searchInput.value.toLowerCase();
  const filter = filterSelect.value;
  const sort = sortSelect.value;

  let filtered = lessons.filter(l =>
    l.title.toLowerCase().includes(search) &&
    (filter === '' || l.level === filter)
  );

  if (sort === 'newest') filtered.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
  if (sort === 'oldest') filtered.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
  if (sort === 'az') filtered.sort((a, b) => a.title.localeCompare(b.title));
  if (sort === 'za') filtered.sort((a, b) => b.title.localeCompare(a.title));

  renderLessons(filtered);
}

searchInput.addEventListener('input', applyFilters);
filterSelect.addEventListener('change', applyFilters);
sortSelect.addEventListener('change', applyFilters);

