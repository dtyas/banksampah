import axios, { AxiosError, type AxiosResponse } from 'axios';
import { toast } from 'vue3-toastify';

type ApiResponse<T = unknown> = {
    status?: boolean;
    message?: string;
    data?: T;
};

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: import.meta.env.VITE_USE_COOKIE_AUTH === 'true',
    headers: {
        Accept: 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('banksampah_token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

api.interceptors.response.use(
    (response: AxiosResponse<ApiResponse>) => {
        const message = response.data?.message;
        if (message) {
            if (response.data?.status === false) {
                toast.error(message);
            } else {
                toast.success(message);
            }
        }
        return response;
    },
    (error: AxiosError<ApiResponse>) => {
        const message = error.response?.data?.message || error.message || 'Request gagal';
        toast.error(message);
        return Promise.reject(error);
    }
);

export default api;
