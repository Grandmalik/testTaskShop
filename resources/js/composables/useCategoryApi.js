import { useApi } from '@/composables/useApi';

export function useCategoryApi() {
    const { get } = useApi();

    const getCategories = () => get('/api/categories');

    return { getCategories };
}
