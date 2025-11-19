import axiosAdmin from "../../utils/axiosAdmin.js";
import axiosClient from "../../utils/axiosClient.js";
import { API_ROUTES } from "../../routes.js";

const exerciseApi = {
  async importExcel(file) {
    const formData = new FormData();
    formData.append("file", file);

    // const response = await axiosAdmin.post(API_ROUTES.exercise.importExcel, formData, {
    //   headers: {
    //     "Content-Type": "multipart/form-data",
    //   },
    // });

    const response = await axiosClient.post(API_ROUTES.exercise.importExcel, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data;
  }

};

export default exerciseApi;