// Navigation logic
export function getAllLessons(navigationData) {
    const lessonsMap = new Map();
    for (const type of navigationData) {
        if (!Array.isArray(type.lessons)) continue;
        for (const lesson of type.lessons) {
            const lessonId = Number(lesson.id);
            if (!lessonsMap.has(lessonId)) {
                lessonsMap.set(lessonId, { id: lesson.id, title: lesson.title });
            }
        }
    }
    return Array.from(lessonsMap.values());
}

export function getFirstQuestionIdFromExercise(exerciseItem) {
    if (!exerciseItem) return null;
    if (exerciseItem.first_question_id) return exerciseItem.first_question_id;
    if (Array.isArray(exerciseItem.questions) && exerciseItem.questions.length > 0) {
        return exerciseItem.questions[0].id ?? null;
    }
    return null;
}

export function lessonHasTypes(lessonId, navigationData) {
    if (!lessonId) return false;
    for (const type of navigationData) {
        const lessonData = type.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
        if (lessonData && Array.isArray(lessonData.exercises) && lessonData.exercises.length > 0) {
            const hasQuestions = lessonData.exercises.some((exercise) => {
                return getFirstQuestionIdFromExercise(exercise) !== null;
            });
            if (hasQuestions) return true;
        }
    }
    return false;
}

export function getAvailableTypesForLesson(lessonId, navigationData) {
    if (!lessonId) return [];
    const availableTypes = [];
    for (const type of navigationData) {
        const lessonData = type.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
        if (lessonData && Array.isArray(lessonData.exercises) && lessonData.exercises.length > 0) {
            const hasQuestions = lessonData.exercises.some((exercise) => {
                return getFirstQuestionIdFromExercise(exercise) !== null;
            });
            if (hasQuestions) availableTypes.push(type);
        }
    }
    return availableTypes;
}

export function getAvailableLessonsForType(typeCode, navigationData) {
    if (!typeCode) return [];
    const typeData = navigationData.find((type) => type.code === typeCode);
    if (!typeData) return [];
    return Array.isArray(typeData.lessons) ? typeData.lessons : [];
}

export function getFirstQuestionIdFromLesson(typeCode, lessonId, navigationData) {
    if (!typeCode || !lessonId) return null;
    const typeData = navigationData.find((type) => type.code === typeCode);
    if (!typeData) return null;
    const lessonData = typeData.lessons?.find((lesson) => Number(lesson.id) === Number(lessonId));
    if (!lessonData) return null;
    const exercises = Array.isArray(lessonData.exercises) ? lessonData.exercises : [];
    for (const exercise of exercises) {
        const questionId = getFirstQuestionIdFromExercise(exercise);
        if (questionId) return Number(questionId);
    }
    return null;
}

export function getFirstQuestionIdForType(typeCode, navigationData) {
    const typeData = navigationData.find((type) => type.code === typeCode);
    if (!typeData || !Array.isArray(typeData.lessons)) return null;
    for (const lesson of typeData.lessons) {
        if (!Array.isArray(lesson.exercises)) continue;
        for (const exercise of lesson.exercises) {
            const questionId = getFirstQuestionIdFromExercise(exercise);
            if (questionId) return Number(questionId);
        }
    }
    return null;
}

export function renderTypeOptions(lessonId, activeTypeCode, typeSelect, navigationData) {
    if (!typeSelect) return;
    const allTypes = navigationData;
    const availableTypesForLesson = lessonId ? getAvailableTypesForLesson(lessonId, navigationData) : [];
    
    if (!allTypes.length) {
        typeSelect.innerHTML = '<option value="">Không có dạng bài</option>';
        typeSelect.disabled = true;
        return;
    }

    const optionsHtml = allTypes.map((type) => {
        const label = type.name || type.code;
        const selected = type.code === activeTypeCode ? 'selected' : '';
        const isAvailable = !lessonId || availableTypesForLesson.some((t) => t.code === type.code);
        return `<option value="${type.code}" ${selected} ${!isAvailable ? 'style="color: #999;"' : ''}>${label}</option>`;
    }).join('');

    typeSelect.innerHTML = optionsHtml;
    
    if (lessonId) {
        typeSelect.disabled = availableTypesForLesson.length <= 1;
    } else {
        typeSelect.disabled = false;
    }
}

export function updateLessonOptions(typeCode, activeLessonId, lessonSelect, navigationData) {
    if (!lessonSelect) return;
    const allLessons = getAllLessons(navigationData);
    
    if (!allLessons.length) {
        lessonSelect.innerHTML = '<option value="">Không có bài học</option>';
        lessonSelect.disabled = true;
        return;
    }

    const lessonsInType = typeCode ? getAvailableLessonsForType(typeCode, navigationData).map((l) => Number(l.id)) : [];
    const optionsHtml = allLessons.map((lesson) => {
        const lessonId = Number(lesson.id);
        const selected = lessonId === Number(activeLessonId) ? 'selected' : '';
        const hasTypes = lessonHasTypes(lessonId, navigationData);
        const disabled = !hasTypes ? 'disabled' : '';
        const style = !hasTypes ? 'style="color: #999;"' : '';
        return `<option value="${lesson.id}" ${selected} ${disabled} ${style}>${lesson.title}</option>`;
    }).join('');

    lessonSelect.innerHTML = optionsHtml;
    lessonSelect.disabled = false;
    
    if (activeLessonId && !allLessons.some((lesson) => Number(lesson.id) === activeLessonId)) {
        const firstAvailableLesson = allLessons.find((lesson) => lessonHasTypes(Number(lesson.id), navigationData));
        if (firstAvailableLesson) {
            return Number(firstAvailableLesson.id);
        }
    }
    return activeLessonId;
}

