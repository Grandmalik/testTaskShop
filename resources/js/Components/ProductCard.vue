<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 flex flex-col">
        <div class="p-5 flex-1">
            <span class="inline-block text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full mb-3">
                {{ product.category?.name ?? '—' }}
            </span>
            <h3 class="text-base font-semibold text-gray-800 mb-2 line-clamp-2">
                {{ product.name }}
            </h3>
            <p class="text-sm text-gray-500 line-clamp-3">
                {{ product.description || 'Описание отсутствует' }}
            </p>
        </div>
        <div class="px-5 pb-5 flex items-center justify-between">
            <span class="text-lg font-bold text-gray-900">
                {{ formatPrice(product.price) }}
            </span>
            <Link
                :href="route('product.show', { id: product.id })"
                class="flex items-center gap-1 text-sm text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors"
            >
                Подробнее
                <ArrowRightIcon class="w-4 h-4" />
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowRightIcon } from '@heroicons/vue/24/outline';
import { useFormatPrice } from '@/composables/useFormatPrice';

defineProps({
    product: { type: Object, required: true },
});

const { formatPrice } = useFormatPrice();

</script>
