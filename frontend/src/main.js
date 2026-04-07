import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './style.css';
import '../assets/css/app.css';
import 'vue-sonner/style.css';
import { toast } from 'vue-sonner';

const app = createApp(App);

window.APP_API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
window.toast = toast;

app.use(createPinia());
app.use(router);
app.mount('#app');
