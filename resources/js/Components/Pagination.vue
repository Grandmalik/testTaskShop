<template>
    <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2 mt-8">
        <button
            @click="$emit('change', meta.current_page - 1)"
            :disabled="meta.current_page === 1"
            class="p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        >
            <ChevronLeftIcon class="w-4 h-4" />
        </button>

        <template v-for="page in pages" :key="page">
            <span v-if="page === '...'" class="px-2 text-gray-400">...</span>
            <button
                v-else
                @click="$emit('change', page)"
                :class="[
                    'w-9 h-9 rounded-lg border text-sm transition-colors',
                    page === meta.current_page
                        ? 'bg-indigo-600 border-indigo-600 text-white'
                        : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                ]"
            >
                {{ page }}
            </button>
        </template>

        <button
            @click="$emit('change', meta.current_page + 1)"
            :disabled="meta.current_page === meta.last_page"
            class="p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        >
            <ChevronRightIcon class="w-4 h-4" />
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    meta: { type: Object, required: true },
});

defineEmits(['change']);

const pages = computed(() => {
    const { current_page, last_page } = props.meta;
    const range = [];

    if (last_page <= 7) {
        for (let i = 1; i <= last_page; i++) range.push(i);
        return range;
    }

    range.push(1);
    if (current_page > 3) range.push('...');
    for (let i = Math.max(2, current_page - 1); i <= Math.min(last_page - 1, current_page + 1); i++) {
        range.push(i);
    }
    if (current_page < last_page - 2) range.push('...');
    range.push(last_page);

    return range;
});
</script>
