<template>
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2 flex-wrap">
        <template v-for="(crumb, index) in crumbs" :key="index">
            <ChevronRightIcon v-if="index > 0" class="w-3 h-3 text-gray-300 flex-shrink-0" />
            <span v-if="index === crumbs.length - 1" class="text-gray-600 font-medium truncate max-w-xs">
                {{ crumb.label }}
            </span>
            <Link v-else :href="crumb.href" class="hover:text-indigo-600 transition-colors whitespace-nowrap">
                {{ crumb.label }}
            </Link>
        </template>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from '@heroicons/vue/24/outline';

defineProps({
    crumbs: { type: Array, required: true },
});
</script>
