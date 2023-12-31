import axios from "axios";

const axiosInstance = axios.create({
    baseURL: "https://site-bilete.test",
});

axiosInstance.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default axiosInstance;
