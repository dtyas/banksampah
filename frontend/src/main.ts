import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './style.css';
import '../assets/css/app.css';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const app = createApp(App);

window.APP_API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

app.use(createPinia());
app.use(Vue3Toastify, {
    position: 'top-right',
    closeButton: true,
});
app.use(router);
app.mount('#app');
