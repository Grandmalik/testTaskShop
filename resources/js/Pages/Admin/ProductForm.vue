<template>
    <Head :title="title + ' | ' + form?.name ?? ''" />
    <AdminLayout
        v-if="canRender"
        :crumbs="[
            { label: 'Управление товарами', href: route('admin.products.index') },
            { label: isEdit ? 'Редактировать товар' : 'Добавить товар' },
        ]"
    >
        <div class="max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">
                {{ isEdit ? 'Редактировать товар' : 'Добавить товар' }}
            </h1>

            <div v-if="fetchingProduct" class="space-y-4">
                <div v-for="i in 4" :key="i" class="h-12 bg-gray-100 rounded-lg animate-pulse" />
            </div>

            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div v-if="serverError" class="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    {{ serverError }}
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Название <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Введите название товара"
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="errors.name ? 'border-red-400' : 'border-gray-200'"
                        />
                        <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Категория <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.category_id"
                            class="w-full px-4 py-2 border rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="errors.category_id ? 'border-red-400' : 'border-gray-200'"
                        >
                            <option :value="null" disabled>Выберите категорию</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <p v-if="errors.category_id" class="mt-1 text-xs text-red-500">{{ errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Цена <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.price"
                            type="number"
                            min="0.01"
                            step="0.01"
                            placeholder="0.00"
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="errors.price ? 'border-red-400' : 'border-gray-200'"
                        />
                        <p v-if="errors.price" class="mt-1 text-xs text-red-500">{{ errors.price }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Введите описание товара"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                        />
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button
                            @click="handleSubmit"
                            :disabled="submitting"
                            class="flex-1 flex items-center justify-center gap-2 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <CheckIcon class="w-4 h-4" />
                            {{ submitting ? 'Сохраняю...' : (isEdit ? 'Сохранить' : 'Создать') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { CheckIcon } from '@heroicons/vue/24/outline';
import { useProductApi } from '@/composables/useProductApi';
import { useCategoryApi } from '@/composables/useCategoryApi';
import { useAdminGuard } from '@/composables/useAdminGuard';

const { canRender } = useAdminGuard();

const props = defineProps({
    id: { type: [String, Number], default: null },
    title: { type: [String], default: null }
});

const { getProduct, createProduct, updateProduct } = useProductApi();
const { getCategories }                            = useCategoryApi();

const isEdit          = computed(() => !!props.id);
const categories      = ref([]);
const submitting      = ref(false);
const fetchingProduct = ref(false);
const serverError     = ref('');

const form = reactive({ name: '', description: '', price: '', category_id: null });
const errors = reactive({ name: '', price: '', category_id: '' });

const validate = () => {
    errors.name        = '';
    errors.price       = '';
    errors.category_id = '';
    let valid          = true;
    if (!form.name.trim())                  { errors.name        = 'Название обязательно';       valid = false; }
    if (!form.price || Number(form.price) <= 0) { errors.price   = 'Цена должна быть больше 0'; valid = false; }
    if (!form.category_id)                  { errors.category_id = 'Выберите категорию';         valid = false; }
    return valid;
};

const handleSubmit = async () => {
    if (!validate()) return;
    submitting.value  = true;
    serverError.value = '';
    try {
        const payload = {
            name:        form.name,
            description: form.description,
            price:       Number(form.price),
            category_id: form.category_id,
        };
        if (isEdit.value) {
            await updateProduct(props.id, payload);
        } else {
            await createProduct(payload);
        }
        router.visit(route('admin.products.index'));
    } catch (e) {
        const data = e.response?.data;
        if (e.response?.status === 422 && data?.errors) {
            errors.name        = data.errors.name?.[0]        ?? '';
            errors.price       = data.errors.price?.[0]       ?? '';
            errors.category_id = data.errors.category_id?.[0] ?? '';
        } else {
            serverError.value = 'Ошибка сервера. Попробуйте позже.';
        }
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    try {
        const res    = await getCategories();
        categories.value = res.data;
    } catch (e) {
        console.error(e);
    }

    if (isEdit.value) {
        fetchingProduct.value = true;
        try {
            const res        = await getProduct(props.id);
            const product    = res.data;
            form.name        = product.name;
            form.description = product.description ?? '';
            form.price       = product.price;
            form.category_id = product.category_id;
        } catch (e) {
            serverError.value = 'Не удалось загрузить товар';
        } finally {
            fetchingProduct.value = false;
        }
    }
});
</script>
