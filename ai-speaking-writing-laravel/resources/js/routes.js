export const API_ROUTES = {
  question: {
    base: "/questions",
    show: (id) => `/questions/${id}`,
    checkResult: (id) => `/questions/${id}/check-result`,
    speechToText: `http://127.0.0.1:5000/stt`,
    textToSpeech: `http://127.0.0.1:5000/tts`,
  },

  lesson: {
    base: "/lessons",
    show: (id) => `/lessons/${id}`,
  },
};
