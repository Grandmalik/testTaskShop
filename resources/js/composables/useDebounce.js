import { ref, watch } from 'vue';

export function useDebounce(value, delay = 400) {
    const debouncedValue = ref(value.value);
    let timer = null;

    watch(value, (newValue) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            debouncedValue.value = newValue;
        }, delay);
    });

    return debouncedValue;
}
