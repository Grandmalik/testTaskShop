import { useApi } from '@/composables/useApi';

export function useProductApi() {
    const { get, post, put, destroy } = useApi();

    const getProducts = (params = {}) => get('/api/products', params);
    const getProduct  = (id)          => get(`/api/products/${id}`);
    const createProduct = (payload)   => post('/api/products', payload);
    const updateProduct = (id, payload) => put(`/api/products/${id}`, payload);
    const deleteProduct = (id)        => destroy(`/api/products/${id}`);

    return { getProducts, getProduct, createProduct, updateProduct, deleteProduct };
}
