<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-black/40" @click="$emit('cancel')" />
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6 z-10">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                            <TrashIcon class="w-6 h-6 text-red-600" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ title }}</h3>
                        <p class="text-sm text-gray-500 mb-6">{{ message }}</p>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="$emit('cancel')"
                            class="flex-1 py-2 px-4 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm"
                        >
                            Отмена
                        </button>
                        <button
                            @click="$emit('confirm')"
                            class="flex-1 flex items-center justify-center gap-2 py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm"
                        >
                            <TrashIcon class="w-4 h-4" />
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline';

defineProps({
    show:    { type: Boolean, default: false },
    title:   { type: String,  default: 'Подтвердите удаление' },
    message: { type: String,  default: 'Это действие невозможно отменить.' },
});

defineEmits(['confirm', 'cancel']);
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to       { opacity: 0; }
</style>
