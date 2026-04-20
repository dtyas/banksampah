import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { canAccessRoute, getFirstAccessibleRoute } from '../features/auth/access-control';
import LoginView from '../features/auth/views/LoginView.vue';
import ForgotPasswordView from '../features/auth/views/ForgotPasswordView.vue';
import ResetPasswordView from '../features/auth/views/ResetPasswordView.vue';
import AdminShell from '../features/legacy/AdminShell.vue';
import DashboardPage from '../features/admin/views/DashboardPage.vue';
import NasabahPage from '../features/admin/views/NasabahPage.vue';
import KategoriSampahPage from '../features/admin/views/KategoriSampahPage.vue';
import SampahPage from '../features/admin/views/SampahPage.vue';
import TransaksiPage from '../features/admin/views/TransaksiPage.vue';
import PembayaranPage from '../features/admin/views/PembayaranPage.vue';
import PencairanSaldoPage from '../features/admin/views/PencairanSaldoPage.vue';
import UserPage from '../features/admin/views/UserPage.vue';
import LaporanPage from '../features/admin/views/LaporanPage.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: { guest: true },
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPasswordView,
        meta: { guest: true },
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPasswordView,
        meta: { guest: true },
    },
    {
        path: '/',
        component: AdminShell,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: DashboardPage,
            },
            {
                path: 'nasabah',
                name: 'nasabah',
                component: NasabahPage,
            },
            {
                path: 'kategori-sampah',
                name: 'kategori-sampah',
                component: KategoriSampahPage,
            },
            {
                path: 'sampah',
                name: 'sampah',
                component: SampahPage,
            },
            {
                path: 'transaksi',
                name: 'transaksi',
                component: TransaksiPage,
            },
            {
                path: 'pembayaran',
                name: 'pembayaran',
                component: PembayaranPage,
            },
            {
                path: 'pencairan-saldo',
                name: 'pencairan-saldo',
                component: PencairanSaldoPage,
            },
            {
                path: 'user',
                name: 'user',
                component: UserPage,
            },
            {
                path: 'laporan',
                name: 'laporan',
                component: LaporanPage,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.token) {
        return { name: 'login' };
    }

    if (authStore.token && !authStore.user) {
        try {
            await authStore.hydrateUser();
        } catch {
            await authStore.signOut();
            return { name: 'login' };
        }
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return { name: getFirstAccessibleRoute(authStore.user) };
    }

    if (to.meta.requiresAuth) {
        const routeName = String(to.name ?? 'dashboard');
        if (!canAccessRoute(authStore.user, routeName)) {
            return { name: getFirstAccessibleRoute(authStore.user) };
        }
    }

    return true;
});

export default router;
