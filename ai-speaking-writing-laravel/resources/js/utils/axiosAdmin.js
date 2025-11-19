import axios from "axios";

const axiosAdmin = axios.create({
  baseURL: "/admin", 
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

axiosAdmin.interceptors.response.use(
  (res) => res,
  (err) => {
    console.error("ADMIN API Error:", err);
    return Promise.reject(err);
  }
);

export default axiosAdmin;
