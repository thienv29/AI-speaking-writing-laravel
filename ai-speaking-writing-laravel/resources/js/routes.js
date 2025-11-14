export const API_ROUTES = {
  question: {
    base: "/questions",
    show: (id) => `/questions/${id}`,
    evaluateAnswer: `/attempts`,
    speechToText: `/stt`,  // Dùng Laravel proxy (baseURL="/api" đã có sẵn)
    textToSpeech: `/tts`,   // Dùng Laravel proxy (baseURL="/api" đã có sẵn)
  },
};