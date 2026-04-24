<template>
    <Head :title="title" />
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center">
                        <ShoppingBagIcon class="w-7 h-7 text-white" />
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-indigo-600">Каталог</h1>
                <p class="text-gray-500 mt-2">Административная панель</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Вход</h2>

                <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    {{ errorMessage }}
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="admin@catalog.com"
                            @keyup.enter="handleLogin"
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="errors.email ? 'border-red-400' : 'border-gray-200'"
                        />
                        <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="••••••••"
                            @keyup.enter="handleLogin"
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="errors.password ? 'border-red-400' : 'border-gray-200'"
                        />
                        <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</p>
                    </div>

                    <button
                        @click="handleLogin"
                        :disabled="loading"
                        class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <ArrowRightOnRectangleIcon class="w-4 h-4" />
                        {{ loading ? 'Вхожу...' : 'Войти' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import {Head, router} from '@inertiajs/vue3';
import { ShoppingBagIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';
import { useAuth } from '@/composables/useAuth';

const { login } = useAuth();

const props = defineProps({
    title: { type: [String], default: null }
});

const loading      = ref(false);
const errorMessage = ref('');

const form = reactive({ email: '', password: '' });
const errors = reactive({ email: '', password: '' });

const validate = () => {
    errors.email    = '';
    errors.password = '';
    let valid       = true;
    if (!form.email)    { errors.email    = 'Email обязателен'; valid = false; }
    if (!form.password) { errors.password = 'Пароль обязателен'; valid = false; }
    return valid;
};

const handleLogin = async () => {
    if (!validate()) return;
    loading.value      = true;
    errorMessage.value = '';
    try {
        await login(form.email, form.password);
        router.visit(route('admin.products.index'));
    } catch (e) {
        errorMessage.value = e.response?.status === 422
            ? 'Неверный логин или пароль'
            : 'Ошибка сервера. Попробуйте позже';
    } finally {
        loading.value = false;
    }
};
</script>
