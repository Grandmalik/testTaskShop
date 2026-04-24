import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useAuth } from '@/composables/useAuth';

export function useAdminGuard() {
    const { isAuthenticated, initAuth } = useAuth();

    // Инициализируем токен синхронно до рендера
    initAuth();

    // Если не авторизован — сразу редиректим, не ждём onMounted
    if (!isAuthenticated.value) {
        router.visit('/admin/login');
    }

    // Флаг — показывать контент только если авторизован
    const canRender = computed(() => isAuthenticated.value);

    return { canRender };
}
