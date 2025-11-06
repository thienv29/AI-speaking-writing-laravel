import axiosClient from "../utils/axiosClient.js";
import { API_ROUTES } from "../routes.js";

const questionApi = {
  async getById(id) {
    const res = await axiosClient.get(API_ROUTES.question.show(id));
    return res.data;
  },

  async checkResult(questionId, userAnswer, audioBlob = null) {
    const fd = new FormData();
    fd.append('user_answer', userAnswer);

    if (audioBlob) {
        let filename = 'rec.webm';
        if (audioBlob.type.includes('mp4')) filename = 'rec.m4a';
        if (audioBlob.type.includes('ogg')) filename = 'rec.ogg';
        if (audioBlob.type.includes('wav')) filename = 'rec.wav';
        fd.append('user_audio', audioBlob, filename);
    }

    const res = await axiosClient.post(API_ROUTES.question.checkResult(questionId), fd, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
    return res.data;
  },

  async transcribeAudio(audioBlob) {
    let filename = 'rec.webm';
    if (audioBlob.type.includes('mp4')) filename = 'rec.m4a';
    if (audioBlob.type.includes('ogg')) filename = 'rec.ogg';
    if (audioBlob.type.includes('wav')) filename = 'rec.wav';

    const fd = new FormData();
    fd.append('file', audioBlob, filename); 

    const res = await axiosClient.post(API_ROUTES.question.speechToText, fd, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    return res.data;
  },

  async textToSpeech(text, lang='en') {
    const fd = new FormData();
    fd.append('text', text);
    fd.append('lang', lang);

    const res = await axiosClient.post(API_ROUTES.question.textToSpeech, 
      {text, lang}, {responseType: "blob"});

    const audioBlob = res.data;
    const audioUrl = URL.createObjectURL(audioBlob);
    return audioUrl;
  },
};

export default questionApi;
