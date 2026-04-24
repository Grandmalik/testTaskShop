<template>
    <Head :title="title + ' | ' + product?.name ?? ''" />
    <PublicLayout :crumbs="[
        { label: 'Каталог', href: route('home') },
        { label: product?.name ?? '...' },
    ]">
        <div v-if="loading" class="animate-pulse">
            <div class="h-8 bg-gray-200 rounded w-1/3 mb-4" />
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-8" />
            <div class="h-32 bg-gray-200 rounded mb-4" />
        </div>

        <div v-else-if="errorMessage" class="text-center py-20">
            <div class="flex justify-center mb-4">
                <ExclamationCircleIcon class="w-16 h-16 text-gray-300" />
            </div>
            <h2 class="text-2xl font-bold text-gray-700 mb-2">{{ errorMessage }}</h2>
            <Link :href="route('home')" class="text-indigo-600 hover:underline">← Вернуться в каталог</Link>
        </div>

        <div v-else-if="product">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <span class="inline-block text-xs font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full mb-4">
                    {{ product.category?.name ?? '—' }}
                </span>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>
                <div class="text-2xl font-bold text-indigo-600 mb-6">
                    {{ formatPrice(product.price) }}
                </div>
                <hr class="border-gray-100 mb-6" />
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Описание</h2>
                    <p class="text-gray-700 leading-relaxed">{{ product.description || 'Описание отсутствует' }}</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import {Head, Link} from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ArrowLeftIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';
import { useProductApi } from '@/composables/useProductApi';
import { useApi } from '@/composables/useApi';
import { useFormatPrice } from '@/composables/useFormatPrice';

const props = defineProps({
    id: { type: [String, Number], required: true },
    title: { type: [String], default: null }
});

const { getProduct }      = useProductApi();
const { getErrorMessage } = useApi();

const product      = ref(null);
const loading      = ref(false);
const errorMessage = ref('');

const fetchProduct = async () => {
    loading.value      = true;
    errorMessage.value = '';
    try {
        const response = await getProduct(props.id);
        product.value  = response.data;
    } catch (e) {
        errorMessage.value = getErrorMessage(e);
    } finally {
        loading.value = false;
    }
};

const { formatPrice } = useFormatPrice();

const formatDate = (date) => new Date(date).toLocaleDateString('ru-RU');

watch(() => props.id, fetchProduct);
onMounted(fetchProduct);
</script>
