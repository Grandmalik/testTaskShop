import axios from 'axios';

export function useApi() {
    const get = async (url, params = {}) => {
        const { data } = await axios.get(url, { params });
        return data;
    };

    const post = async (url, payload = {}) => {
        const { data } = await axios.post(url, payload);
        return data;
    };

    const put = async (url, payload = {}) => {
        const { data } = await axios.put(url, payload);
        return data;
    };

    const destroy = async (url) => {
        const { data } = await axios.delete(url);
        return data;
    };

    // Хелпер для извлечения сообщения ошибки
    const getErrorMessage = (error) => {
        if (error.response?.status === 404) return 'Запись не найдена';
        if (error.response?.status === 403) return 'Нет доступа';
        if (error.response?.status === 401) return 'Необходима авторизация';
        if (error.response?.status === 422) return 'Ошибка валидации';
        if (error.response?.status >= 500) return 'Ошибка сервера. Попробуйте позже';
        return 'Произошла неизвестная ошибка';
    };

    return { get, post, put, destroy, getErrorMessage };
}
