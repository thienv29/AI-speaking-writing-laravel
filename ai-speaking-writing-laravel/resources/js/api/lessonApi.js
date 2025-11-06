import axiosClient from "../utils/axiosClient.js";
import { API_ROUTES } from "../routes.js";

const lessonApi = {
    async getAll() {
        const res = await axiosClient.get(API_ROUTES.lesson.base);
        return res.data;
    },

    async getById(id) {
        const res = await axiosClient.get(API_ROUTES.lesson.show(id));
        return res.data;
    },
};

export default lessonApi;
