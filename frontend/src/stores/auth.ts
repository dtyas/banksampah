import { defineStore } from 'pinia';
import type { AuthLoginPayload, AuthUser } from '../api/auth.api';
import { getMe, login, logout } from '../api/auth.api';

type AuthState = {
    user: AuthUser | null;
    token: string | null;
    loading: boolean;
};

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        user: null,
        token: localStorage.getItem('banksampah_token'),
        loading: false,
    }),
    getters: {
        isAuthenticated: (state) => Boolean(state.token),
    },
    actions: {
        async signIn(payload: AuthLoginPayload) {
            this.loading = true;

            try {
                const response = await login(payload);

                if (!response.status) {
                    throw new Error(response.message || 'Login gagal');
                }

                this.token = response.data.token;
                this.user = response.data.user;
                if (this.token) {
                    localStorage.setItem('banksampah_token', this.token);
                }
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
