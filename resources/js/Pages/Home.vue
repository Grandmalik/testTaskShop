<template>
    <Head :title="title" />
    <PublicLayout>
        <div>
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Каталог товаров</h1>
            </div>

            <!-- Фильтры -->
            <div class="flex flex-wrap gap-3 mb-6">
                <!-- Поиск -->
                <div class="relative w-full mb-3">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Поиск товаров..."
                        class="w-full pl-9 pr-9 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <XMarkIcon class="w-4 h-4" />
                    </button>
                </div>

                <!-- Фильтр по категории -->
                <select
                    v-model="selectedCategory"
                    class="w-full sm:w-64 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option :value="null">Все категории</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>

                <!-- Количество на странице -->
                <select
                    v-model="perPage"
                    class="w-full sm:w-40 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option :value="10">10 на странице</option>
                    <option :value="12">12 на странице</option>
                    <option :value="15">15 на странице</option>
                </select>
            </div>

            <!-- Ошибка -->
            <div
                v-if="errorMessage"
                class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 flex items-center justify-between"
            >
                <span>{{ errorMessage }}</span>
                <button @click="fetchProducts" class="text-red-600 underline hover:no-underline">
                    Повторить
                </button>
            </div>

            <!-- Загрузка -->
            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="i in perPage" :key="i" class="bg-white rounded-xl border border-gray-100 h-56 animate-pulse" />
            </div>

            <!-- Пусто -->
            <div v-else-if="!errorMessage && !products.length" class="text-center py-20 text-gray-400">
                <div class="flex justify-center mb-4">
                    <InboxIcon class="w-16 h-16 text-gray-300" />
                </div>
                <p v-if="debouncedSearch" class="text-lg">
                    Ничего не найдено по запросу «{{ debouncedSearch }}»
                </p>
                <p v-else class="text-lg">Товары не найдены</p>
            </div>

            <!-- Список -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>

            <!-- Пагинация -->
            <Pagination
                v-if="meta && meta.last_page > 1"
                :meta="meta"
                @change="onPageChange"
            />

            <!-- Инфо -->
            <div v-if="meta && !loading" class="text-center text-sm text-gray-400 mt-4">
                Показано {{ products.length }} из {{ meta.total }} товаров
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { useProductApi } from '@/composables/useProductApi';
import { useCategoryApi } from '@/composables/useCategoryApi';
import { useApi } from '@/composables/useApi';
import { useDebounce } from '@/composables/useDebounce';
import {Head} from "@inertiajs/vue3";
import { MagnifyingGlassIcon, XMarkIcon, InboxIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    title: { type: [String], default: null }
});

const { getProducts }     = useProductApi();
const { getCategories }   = useCategoryApi();
const { getErrorMessage } = useApi();

const products         = ref([]);
const categories       = ref([]);
const meta             = ref(null);
const loading          = ref(false);
const errorMessage     = ref('');
const selectedCategory = ref(null);
const currentPage      = ref(1);
const perPage          = ref(10);
const searchQuery      = ref('');

// Дебаунс — запрос уходит только через 400мс после остановки печати
const debouncedSearch = useDebounce(searchQuery, 400);

const fetchProducts = async () => {
    loading.value      = true;
    errorMessage.value = '';
    try {
        const params = {
            page:     currentPage.value,
            per_page: perPage.value,
        };
        if (selectedCategory.value)    params.category_id = selectedCategory.value;
        if (debouncedSearch.value)     params.search      = debouncedSearch.value;

        const response = await getProducts(params);
        products.value = response.data ?? [];
        meta.value     = response.meta ?? null;
    } catch (e) {
        errorMessage.value = getErrorMessage(e);
        products.value     = [];
    } finally {
        loading.value = false;
    }
};

const fetchCategories = async () => {
    try {
        const response   = await getCategories();
        categories.value = response.data ?? [];
    } catch (e) {
        console.error(e);
    }
};

// Сброс страницы и перезапрос при изменении дебаунс-значения
watch(debouncedSearch, () => {
    currentPage.value = 1;
    fetchProducts();
});

watch(selectedCategory, () => {
    currentPage.value = 1;
    fetchProducts();
});

watch(perPage, () => {
    currentPage.value = 1;
    fetchProducts();
});

const onPageChange = (page) => {
    currentPage.value = page;
    fetchProducts();
};

onMounted(() => {
    fetchCategories();
    fetchProducts();
});
</script>
