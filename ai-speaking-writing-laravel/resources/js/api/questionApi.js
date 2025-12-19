import axiosClient from "../utils/axiosClient.js";
import { API_ROUTES } from "../routes.js";

const questionApi = {
  async getById(id) {
    const res = await axiosClient.get(API_ROUTES.question.show(id));
    return res.data;
  },

  async evaluateAnswer(userId, questionId, userAnswer, audioBlob = null) {
    const fd = new FormData();
    // Chỉ append user_id nếu có giá trị hợp lệ
    if (userId && userId !== null && userId !== undefined && userId !== '') {
      fd.append('user_id', parseInt(userId, 10));
    }
    fd.append('user_answer', userAnswer || '');
    fd.append('question_id', parseInt(questionId, 10));

    if (audioBlob) {
        let filename = 'rec.webm';
        if (audioBlob.type.includes('mp4')) filename = 'rec.m4a';
        if (audioBlob.type.includes('ogg')) filename = 'rec.ogg';
        if (audioBlob.type.includes('wav')) filename = 'rec.wav';
        fd.append('user_audio', audioBlob, filename);
    }

    const res = await axiosClient.post(API_ROUTES.question.evaluateAnswer, fd, {
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

    // Dùng axiosClient vì bây giờ là relative URL (Laravel proxy)
    const res = await axiosClient.post(API_ROUTES.question.speechToText, fd, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: 30000, // 30 seconds timeout
    });

    return res.data;
  },

  async textToSpeech(text, lang='en') {
    try {
      if (!text || !text.trim()) {
        throw new Error('Text is empty');
      }

      const requestData = {text: text.trim(), lang};
      const ttsUrl = API_ROUTES.question.textToSpeech;
      console.log('📞 Calling TTS API (via Laravel proxy):', ttsUrl, {text: text.substring(0, 50) + '...', lang});
      
      // Dùng axiosClient vì bây giờ là relative URL (Laravel proxy)
      const res = await axiosClient.post(
        ttsUrl, 
        requestData,
        {
          responseType: "blob",
          timeout: 20000, // 20 seconds timeout
        }
      );

      // Kiểm tra response
      if (!res || !res.data) {
        throw new Error('TTS API returned no data');
      }

      if (res.data.size === 0) {
        throw new Error('TTS API returned empty blob');
      }

      // Kiểm tra content type
      const contentType = res.headers['content-type'] || res.headers['Content-Type'] || '';
      if (!contentType.includes('audio') && !contentType.includes('mpeg')) {
        console.warn('⚠️ Unexpected content type:', contentType, 'size:', res.data.size);
      }

      const audioBlob = res.data;
      const audioUrl = URL.createObjectURL(audioBlob);
      console.log('✅ TTS API success! Blob size:', audioBlob.size, 'bytes, type:', audioBlob.type || contentType);
      return audioUrl;
    } catch (error) {
      const errorDetails = {
        message: error.message,
        status: error.response?.status,
        statusText: error.response?.statusText,
        data: error.response?.data,
        code: error.code
      };
      console.error('❌ TTS API error:', errorDetails);
      
      throw error;
    }
  },

  async getLessonStatistics(lessonId, userId, resetTimestamp = null) {
    const params = { user_id: userId };
    if (resetTimestamp) {
      params.reset_timestamp = resetTimestamp;
    }
    const res = await axiosClient.get(API_ROUTES.question.getLessonStatistics(lessonId), {
      params
    });
    return res.data;
  },

  async getExerciseStatistics(exerciseId, userId, resetTimestamp = null) {
    const params = { user_id: userId };
    if (resetTimestamp) {
      params.reset_timestamp = resetTimestamp;
    }
    const res = await axiosClient.get(API_ROUTES.question.getExerciseStatistics(exerciseId), {
      params
    });
    return res.data;
  },

  async deleteLessonAttempts(lessonId, userId) {
    const res = await axiosClient.delete(API_ROUTES.question.deleteLessonAttempts(lessonId), {
      params: { user_id: userId }
    });
    return res.data;
  },

  async deleteExerciseAttempts(exerciseId, userId) {
    const res = await axiosClient.delete(API_ROUTES.question.deleteExerciseAttempts(exerciseId), {
      params: { user_id: userId }
    });
    return res.data;
  },
};

export default questionApi;