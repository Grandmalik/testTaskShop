import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept']           = 'application/json';
window.axios.defaults.headers.common['Content-Type']     = 'application/json';

// Глобальный перехватчик ответов
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        // 401 — токен истёк или невалиден → чистим и редиректим на логин
        if (error.response?.status === 401) {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            delete window.axios.defaults.headers.common['Authorization'];

            if (!window.location.pathname.includes('/admin/login')) {
                window.location.href = '/admin/login';
            }
        }

        return Promise.reject(error);
    }
);
