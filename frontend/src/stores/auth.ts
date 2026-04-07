import { defineStore } from 'pinia';
import { getMe, login, logout } from '../api/auth.api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('banksampah_token') || null,
        loading: false,
    }),
    getters: {
        isAuthenticated: (state) => Boolean(state.token),
    },
    actions: {
        async signIn(payload) {
            this.loading = true;

            try {
                const response = await login(payload);

                if (!response.status) {
                    throw new Error(response.message || 'Login gagal');
                }

                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('banksampah_token', this.token);
            } finally {
                this.loading = false;
            }
        },

        async hydrateUser() {
            if (!this.token) {
                return;
            }

            const response = await getMe();
            this.user = response.data;
        },

        async signOut() {
            try {
                await logout();
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('banksampah_token');
            }
        },
    },
});
