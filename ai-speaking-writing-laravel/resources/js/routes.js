export const API_ROUTES = {
  question: {
    base: "/questions",
    show: (id) => `/questions/${id}`,
    evaluateAnswer: `/attempts`,
    speechToText: `http://127.0.0.1:5000/stt`,
    textToSpeech: `http://127.0.0.1:5000/tts`,
  },
};