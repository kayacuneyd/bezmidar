import { browser } from '$app/environment';
import { goto } from '$app/navigation';
import { env as publicEnv } from '$env/dynamic/public';
import { writable } from 'svelte/store';

const DEFAULT_API_URL = 'https://api.dijitalmentor.de';
const API_URL = (publicEnv.PUBLIC_API_URL || DEFAULT_API_URL).replace(/\/$/, '');

function createAuthStore() {
  const { subscribe, set } = writable({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: true
  });

  return {
    subscribe,
    
    checkAuth: async () => {
      if (!browser) return;
      
      const token = localStorage.getItem('bezmidar_token');
      const userStr = localStorage.getItem('bezmidar_user');
      const cachedUser = userStr ? JSON.parse(userStr) : null;

      const applySession = user => {
        localStorage.setItem('bezmidar_token', token);
        localStorage.setItem('bezmidar_user', JSON.stringify(user));
        set({ user, token, isAuthenticated: true, loading: false });
      };

      if (!token) {
        set({ user: null, token: null, isAuthenticated: false, loading: false });
        return;
      }

      try {
        const res = await fetch(`${API_URL}/auth/me.php`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        const payload = await res.json();

        if (res.ok && payload?.data) {
          applySession(payload.data);
          return;
        }
      } catch (err) {
        console.warn('Auth sync failed', err);
      }

      if (cachedUser) {
        applySession(cachedUser);
        return;
      }

      localStorage.removeItem('bezmidar_token');
      localStorage.removeItem('bezmidar_user');
      set({ user: null, token: null, isAuthenticated: false, loading: false });
    },
    
    login: (user, token) => {
      if (browser) {
        localStorage.setItem('bezmidar_token', token);
        localStorage.setItem('bezmidar_user', JSON.stringify(user));
      }
      set({ user, token, isAuthenticated: true, loading: false });
    },
    
    logout: () => {
      if (browser) {
        localStorage.removeItem('bezmidar_token');
        localStorage.removeItem('bezmidar_user');
      }
      set({ user: null, token: null, isAuthenticated: false, loading: false });
      goto('/');
    }
  };
}

export const authStore = createAuthStore();
