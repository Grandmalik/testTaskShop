<template>
    <Head :title="title" />
    <AdminLayout v-if="canRender">
        <ConfirmModal
            :show="!!deletingId"
            title="Удалить товар?"
            message="Товар будет удалён!"
            @confirm="confirmDelete"
            @cancel="deletingId = null"
        />

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Управление товарами</h1>
            <Link
                :href="route('admin.products.create')"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
            >
                <PlusIcon class="w-4 h-4" />
                Добавить товар
            </Link>
        </div>

        <div class="flex flex-wrap gap-3 mb-6">
            <select
                v-model="selectedCategory"
                class="w-full sm:w-64 px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option :value="null">Все категории</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <select
                v-model="perPage"
                class="w-full sm:w-40 px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option :value="10">10 на странице</option>
                <option :value="12">12 на странице</option>
                <option :value="15">15 на странице</option>
            </select>
        </div>

        <div v-if="loading" class="space-y-3">
            <div v-for="i in perPage" :key="i" class="h-16 bg-white rounded-xl border border-gray-100 animate-pulse" />
        </div>

        <template v-else>
            <div v-if="!products.length" class="text-center py-20 text-gray-400">
                <div class="flex justify-center mb-4">
                    <InboxIcon class="w-16 h-16 text-gray-300" />
                </div>
                <p class="text-lg">Товары не найдены</p>
            </div>

            <Transition
                enter-active-class="transition-opacity duration-200 ease-in"
                leave-active-class="transition-opacity duration-200 ease-out"
                enter-from-class="opacity-0"
                leave-to-class="opacity-0"
                mode="out-in"
            >
                <div
                    v-if="products.length"
                    :key="currentPage"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-4 py-3 font-medium text-gray-500">Название</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-500">Категория</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-500">Цена</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-500 hidden md:table-cell">Описание</th>
                            <th class="px-4 py-3 font-medium text-gray-500 text-right">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ product.name }}</td>
                            <td class="px-4 py-3">
                                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-full">
                                        {{ product.category?.name ?? '—' }}
                                    </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-medium">{{ formatPrice(product.price) }}</td>
                            <td class="px-4 py-3 text-gray-400 hidden md:table-cell max-w-xs truncate">
                                {{ product.description || '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('admin.products.edit', { id: product.id })"
                                        class="flex items-center gap-1 text-xs bg-gray-100 hover:bg-indigo-100 hover:text-indigo-700 text-gray-600 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <PencilIcon class="w-3 h-3" />
                                        Редактировать
                                    </Link>
                                    <button
                                        @click="deletingId = product.id"
                                        class="flex items-center gap-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <TrashIcon class="w-3 h-3" />
                                        Удалить
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </Transition>
        </template>

        <Pagination v-if="meta && meta.last_page > 1" :meta="meta" @change="onPageChange" />

        <div v-if="meta && !loading" class="text-center text-sm text-gray-400 mt-4">
            Показано {{ products.length }} из {{ meta.total }} товаров
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import {Head, Link} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { PlusIcon, PencilIcon, TrashIcon, InboxIcon } from '@heroicons/vue/24/outline';
import { useProductApi } from '@/composables/useProductApi';
import { useCategoryApi } from '@/composables/useCategoryApi';
import { useAdminGuard } from '@/composables/useAdminGuard';
import { useFormatPrice } from '@/composables/useFormatPrice';

const props = defineProps({
    title: { type: [String], default: null }
});

const { canRender }                  = useAdminGuard();
const { getProducts, deleteProduct } = useProductApi();
const { getCategories }              = useCategoryApi();

const products         = ref([]);
const categories       = ref([]);
const meta             = ref(null);
const loading          = ref(false);
const selectedCategory = ref(null);
const currentPage      = ref(1);
const perPage          = ref(10);
const deletingId       = ref(null);

const fetchProducts = async () => {
    loading.value = true;
    try {
        const params = { page: currentPage.value, per_page: perPage.value };
        if (selectedCategory.value) params.category_id = selectedCategory.value;
        const response = await getProducts(params);
        products.value = response.data ?? [];
        meta.value     = response.meta ?? null;
    } catch (e) {
        console.error(e);
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

const confirmDelete = async () => {
    try {
        await deleteProduct(deletingId.value);
        if (products.value.length === 1 && currentPage.value > 1) currentPage.value--;
        await fetchProducts();
    } catch (e) {
        console.error(e);
    } finally {
        deletingId.value = null;
    }
};

watch(selectedCategory, () => { currentPage.value = 1; fetchProducts(); });
watch(perPage, () => { currentPage.value = 1; fetchProducts(); });

const onPageChange = (page) => { currentPage.value = page; fetchProducts(); };

const { formatPrice } = useFormatPrice();

onMounted(() => { fetchCategories(); fetchProducts(); });
</script>
