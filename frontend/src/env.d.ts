/// <reference types="vite/client" />

declare module '*.css';

declare module 'vue3-toastify';

declare interface ImportMetaEnv {
    readonly VITE_API_BASE_URL: string;
    readonly VITE_USE_COOKIE_AUTH: string;
}

declare interface ImportMeta {
    readonly env: ImportMetaEnv;
}

declare interface Window {
    APP_API_BASE_URL?: string;
}
