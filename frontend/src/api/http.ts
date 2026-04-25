import axios, { AxiosError, type AxiosResponse } from 'axios';
import { toast } from 'vue3-toastify';

type ApiResponse<T = unknown> = {
    status?: boolean;
    message?: string;
    data?: T;
};

function shouldShowToast(method?: string, url?: string): boolean {
    const normalizedMethod = (method ?? '').toLowerCase();
    const normalizedUrl = (url ?? '').toLowerCase();

    if (['post', 'put', 'patch', 'delete'].includes(normalizedMethod)) {
        return true;
    }

    return normalizedUrl.includes('/auth/login') || normalizedUrl.includes('/auth/logout');
}

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: import.meta.env.VITE_USE_COOKIE_AUTH === 'true',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json', // Tambahkan ini
        'X-Requested-With': 'XMLHttpRequest', // Tambahkan ini untuk membantu Laravel mengenali AJAX
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
        if (message && shouldShowToast(response.config.method, response.config.url)) {
            if (response.data?.status === false) {
                toast.error(message);
            } else {
                toast.success(message);
            }
        }
        return response;
    },
    (error: AxiosError<ApiResponse>) => {
        if (shouldShowToast(error.config?.method, error.config?.url)) {
            const message = error.response?.data?.message || error.message || 'Request gagal';
            toast.error(message);
        }
        return Promise.reject(error);
    }
);

export default api;
