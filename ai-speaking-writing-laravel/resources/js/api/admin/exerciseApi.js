import axiosAdmin from "../../utils/axiosAdmin.js";
import { API_ROUTES } from "../../routes.js";

const exerciseApi = {
  async importExcel(file, lessonId) {
    const formData = new FormData();
    formData.append("file", file);
    if (lessonId) {
      formData.append("lesson_id", lessonId);
    }

    const response = await axiosAdmin.post(API_ROUTES.exercise.importExcel, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data;
  }

};

export default exerciseApi;