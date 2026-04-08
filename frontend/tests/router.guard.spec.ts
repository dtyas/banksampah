import { beforeEach, describe, expect, it } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import router from '../src/router';
import { useAuthStore } from '../src/stores/auth';

describe('router guards', () => {
    beforeEach(async () => {
        localStorage.clear();
        setActivePinia(createPinia());
        await router.push('/login');
        await router.isReady();
    });

    it('redirects unauthenticated user to login', async () => {
        await router.push('/');
        expect(router.currentRoute.value.name).toBe('login');
    });

    it('allows authenticated user to access dashboard', async () => {
        const store = useAuthStore();
        store.token = 'token-123';

        await router.push('/');
        expect(router.currentRoute.value.name).toBe('dashboard');
    });
});
