import { ref, computed } from 'vue';
import { useApi } from '@/composables/useApi';
import { router } from '@inertiajs/vue3';

const token = ref(localStorage.getItem('admin_token') ?? null);
const user  = ref(JSON.parse(localStorage.getItem('admin_user') ?? 'null'));

export function useAuth() {
    const { post } = useApi();

    const isAuthenticated = computed(() => !!token.value);

    const login = async (email, password) => {
        const data = await post('/api/login', { email, password });

        token.value = data.token;
        user.value  = data.user;

        localStorage.setItem('admin_token', data.token);
        localStorage.setItem('admin_user', JSON.stringify(data.user));

        // Вешаем токен на все последующие запросы axios
        setAxiosToken(data.token);
    };

    const logout = async () => {
        try {
            await post('/api/logout');
        } catch (e) {
            // токен уже мог быть невалиден
        } finally {
            clearAuth();
            router.visit('/admin/login');
        }
    };

    const clearAuth = () => {
        token.value = null;
        user.value  = null;
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_user');
        setAxiosToken(null);
    };

    const setAxiosToken = (t) => {
        if (t) {
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${t}`;
        } else {
            delete window.axios.defaults.headers.common['Authorization'];
        }
    };

    // Инициализация токена при загрузке страницы
    const initAuth = () => {
        if (token.value) setAxiosToken(token.value);
    };

    return { token, user, isAuthenticated, login, logout, initAuth, clearAuth };
}
