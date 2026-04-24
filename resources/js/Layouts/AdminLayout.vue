<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <Link :href="route('home')" class="flex items-center gap-2 text-xl font-bold text-indigo-600">
                        <ShoppingBagIcon class="w-6 h-6" />
                        Каталог
                    </Link>
                </div>

                <div class="relative" ref="dropdownRef">
                    <button
                        @click="dropdownOpen = !dropdownOpen"
                        class="flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                    >
                        <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                            {{ userInitial }}
                        </span>
                        <span>{{ user?.name }}</span>
                        <ChevronDownIcon v-if="!dropdownOpen" class="w-4 h-4 text-gray-400" />
                        <ChevronUpIcon v-else class="w-4 h-4 text-gray-400" />
                    </button>

                    <Transition
                        enter-active-class="transition ease-out duration-150"
                        enter-from-class="opacity-0 -translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-1"
                    >
                        <div
                            v-if="dropdownOpen"
                            class="absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                        >
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs font-medium text-gray-800 truncate">{{ user?.name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ user?.email }}</p>
                            </div>

                            <Link
                                :href="route('admin.products.index')"
                                @click="dropdownOpen = false"
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors"
                            >
                                <CubeIcon class="w-4 h-4" />
                                Управление товарами
                            </Link>

                            <div class="border-t border-gray-100 mt-1 pt-1">
                                <button
                                    @click="handleLogout"
                                    :disabled="loggingOut"
                                    class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors disabled:opacity-50"
                                >
                                    <ArrowRightOnRectangleIcon class="w-4 h-4" />
                                    {{ loggingOut ? 'Выход...' : 'Выйти' }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-4 py-8">
            <Breadcrumbs v-if="crumbs.length" :crumbs="crumbs" />
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    ShoppingBagIcon,
    CubeIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { useAuth } from '@/composables/useAuth';

defineProps({
    crumbs: { type: Array, default: () => [] },
});

const { user, logout } = useAuth();
const loggingOut       = ref(false);
const dropdownOpen     = ref(false);
const dropdownRef      = ref(null);

const userInitial = computed(() => user.value?.name?.charAt(0).toUpperCase() ?? 'A');

const isActive = (routeName) => window.location.pathname === route(routeName);

const handleLogout = async () => {
    loggingOut.value   = true;
    dropdownOpen.value = false;
    await logout();
};

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        dropdownOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
