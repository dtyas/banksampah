import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [vue()],
    optimizeDeps: {
        include: ['vue3-toastify'],
    },
    server: {
        host: true,
        port: 5173,
    },
    test: {
        environment: 'jsdom',
        globals: true,
        setupFiles: [],
    },
});
