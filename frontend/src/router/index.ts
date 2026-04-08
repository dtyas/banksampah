import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import LoginPage from '../features/legacy/LoginPage.vue';
import AdminShell from '../features/legacy/AdminShell.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
        meta: { guest: true },
    },
    {
        path: '/',
        name: 'dashboard',
        component: AdminShell,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return { name: 'login' };
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return { name: 'dashboard' };
    }

    return true;
});

export default router;
