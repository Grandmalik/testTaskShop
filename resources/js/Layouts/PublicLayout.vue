<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-2 text-xl font-bold text-indigo-600 hover:text-indigo-700">
                    <ShoppingBagIcon class="w-6 h-6" />
                    Каталог товаров
                </Link>
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.products.index')"
                        class="text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                    >
                        Админ панель
                    </Link>
                </div>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-4 py-8">
            <Breadcrumbs v-if="crumbs.length" :crumbs="crumbs" />
            <slot />
        </main>

        <footer class="border-t border-gray-200 mt-16">
            <div class="max-w-6xl mx-auto px-4 py-6 text-center text-sm text-gray-400">
                © {{ new Date().getFullYear() }} Каталог товаров
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ShoppingBagIcon } from '@heroicons/vue/24/outline';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    crumbs: { type: Array, default: () => [] },
});
</script>
