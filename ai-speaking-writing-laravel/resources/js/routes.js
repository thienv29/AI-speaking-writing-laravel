export const API_ROUTES = {
  question: {
    base: "/questions",
    show: (id) => `/questions/${id}`,
    evaluateAnswer: `/attempts`,
    speechToText: `/stt`,  // Dùng Laravel proxy (baseURL="/api" đã có sẵn)
    textToSpeech: `/tts`,   // Dùng Laravel proxy (baseURL="/api" đã có sẵn)
    getLessonStatistics: (lessonId) => `/lessons/${lessonId}/statistics`,
    getExerciseStatistics: (exerciseId) => `/exercises/${exerciseId}/statistics`,
    deleteLessonAttempts: (lessonId) => `/lessons/${lessonId}/attempts`,
    deleteExerciseAttempts: (exerciseId) => `/exercises/${exerciseId}/attempts`,
  },
  exercise: {
    base: "/exercises",
    importExcel: `/import-excel`,
  }
};