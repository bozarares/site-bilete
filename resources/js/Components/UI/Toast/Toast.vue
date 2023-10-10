<script setup>
import { cva } from 'class-variance-authority';
import { useToastStore } from '@/store/toastStore';
import XMarkIcon from '../Icons/XMarkIcon.vue';
import {
    BellAlertIcon,
    CheckBadgeIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline';
import CheckIcon from '../Icons/CheckIcon.vue';
import { computed } from 'vue';

const toastStore = useToastStore();
const props = defineProps(['modelValue']);
const handleToastRemove = (item, index) => {
    toastStore.removeToast(item.id);
};

const toastClass = (status) => {
    return cva('', {
        variants: {
            status: {
                success: 'bg-green-500 text-black',
                warning: 'bg-amber-500 text-black',
                danger: 'bg-red-500 text-black',
                alert: 'bg-gray-500 text-black',
            },
        },
    })({ status });
};
const iconClass = (status) => {
    return cva('', {
        variants: {
            status: {
                success: 'text-green-500',
                warning: 'text-amber-500',
                danger: 'text-red-500',
                alert: 'text-gray-500',
            },
        },
    })({ status });
};

const toastIcon = (status) => {
    switch (status) {
        case 'success':
            return CheckCircleIcon;
        case 'warning':
            return ExclamationTriangleIcon;
        case 'danger':
            return XCircleIcon;
        default:
            return BellAlertIcon;
    }
};
</script>

<template>
    <Teleport to="body">
        <div class="fixed right-0 bottom-0 m-2">
            <transition-group
                name="slide"
                tag="div"
                class="flex flex-col gap-2 transition-all ease-in-out"
                mode="out-in"
            >
                <div
                    v-for="(item, index) in toastStore.toasts"
                    :key="item.id"
                    class="flex h-16 w-[25rem] transform items-center justify-between rounded-md border-2 bg-white px-2 py-2 shadow-md transition-transform duration-500 ease-in-out"
                >
                    <div
                        class="h-full w-[3px] rounded-full"
                        :class="toastClass(item.status)"
                    ></div>
                    <div class="w-10 px-2" :class="iconClass(item.status)">
                        <component :is="toastIcon(item.status)"></component>
                    </div>
                    <div
                        class="relative flex h-full flex-grow flex-col justify-center px-2"
                    >
                        <div v-if="item.title" class="text-md font-bold">
                            {{ item.title }}
                        </div>
                        <div class="text-sm font-medium text-gray-400">
                            {{ item.message }}
                        </div>
                    </div>
                    <button
                        class="h-6 w-6"
                        @click="handleToastRemove(item, index)"
                    >
                        <component class="h-6 w-6" :is="XMarkIcon"></component>
                    </button>
                </div>
            </transition-group>
        </div>
    </Teleport>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition:
        opacity 0.5s ease,
        transform 0.5s ease;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateY(0.5rem);
}

.slide-leave-from,
.slide-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.slide-move {
    transition: transform 0.5s ease;
}

.slide-move-active {
    transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
}

.slide-move-to,
.slide-move-from {
    transition: transform 0.5s ease;
    transform: translateY(0);
}
</style>
