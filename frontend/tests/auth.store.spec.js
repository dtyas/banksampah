import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useAuthStore } from '../src/stores/auth';

vi.mock('../src/api/auth.api', () => ({
    login: vi.fn(async () => ({
        status: true,
        data: {
            token: 'token-123',
            user: { id: 1, nama: 'Admin Utama', role: 'super_admin' },
        },
    })),
    getMe: vi.fn(async () => ({
        status: true,
        data: { id: 1, nama: 'Admin Utama', role: 'super_admin' },
    })),
    logout: vi.fn(async () => ({ status: true })),
}));

describe('auth store', () => {
    beforeEach(() => {
        localStorage.clear();
        setActivePinia(createPinia());
    });

    it('signIn stores token and user', async () => {
        const store = useAuthStore();
        await store.signIn({ email: 'admin@banksampah.id', password: 'secret' });

        expect(store.isAuthenticated).toBe(true);
        expect(store.user?.nama).toBe('Admin Utama');
        expect(localStorage.getItem('banksampah_token')).toBe('token-123');
    });

    it('signOut clears session', async () => {
        const store = useAuthStore();
        await store.signIn({ email: 'admin@banksampah.id', password: 'secret' });

        await store.signOut();

        expect(store.isAuthenticated).toBe(false);
        expect(store.user).toBe(null);
        expect(localStorage.getItem('banksampah_token')).toBe(null);
    });
});
