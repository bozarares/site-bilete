<script setup>
import { useSlots, computed } from '@vue/runtime-core';
import { cva } from 'class-variance-authority';

const props = defineProps({
    loading: Boolean,
    is: { type: [String, Object], default: 'button' },
    as: String,
    href: String,
    to: String,
    disabled: Boolean,
    method: String,
    options: {
        type: Object,
        default: () => ({
            leftIcon: null,
            rightIcon: null,
            color: 'gray',
        }),
    },
});

const options = computed(() => {
    const defaultOptions = {
        leftIcon: null,
        rightIcon: null,
        color: 'gray',
    };
    return { ...defaultOptions, ...props.options };
});

const slots = useSlots();
const buttonText = computed(() => {
    return slots.default && typeof slots.default === 'function'
        ? slots.default()[0]?.children
        : null;
});
const onlyLeftIcon = computed(() => {
    return (
        options.value.leftIcon && !buttonText.value && !options.value.rightIcon
    );
});
const buttonClass = computed(() => {
    return cva(
        'inline-flex min-h-[35px] select-none items-center justify-center rounded-md px-3 py-0.5 font-semibold tracking-wider transition-colors duration-150 ease-in-out hover:transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:transition',
        {
            variants: {
                color: {
                    gray: 'bg-gray-700 text-white hover:bg-gray-600 focus:ring-gray-700',
                    red: 'bg-red-500 text-white hover:bg-red-400 focus:ring-red-500',
                    blue: 'bg-blue-500 text-white hover:bg-blue-400 focus:ring-blue-500',
                    green: 'bg-green-500 text-white hover:bg-green-400 focus:ring-green-500',
                    amber: 'bg-amber-500 text-white hover:bg-amber-400 focus:ring-amber-500',
                    text: 'bg-transparent text-gray-700 hover:bg-gray-200 hover:text-gray-900 focus:ring-gray-300',
                },
                disabled: {
                    true: '!cursor-not-allowed bg-gray-300 text-gray-500 hover:bg-gray-300 focus:ring-gray-200',
                },
            },
        },
    )({ color: options.value.color, disabled: props.disabled });
});
</script>

<template>
    <component
        :aria-label="buttonText + ' button'"
        :is="props.is"
        :as="props.as"
        :disabled="props.disabled"
        :class="buttonClass"
        :href="props.href"
        :to="props.to"
        :method="props.method"
    >
        <!-- Loading svg -->
        <svg
            v-if="props.loading"
            class="absolute h-5 w-5 animate-spin"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>
        <!-- Left icon -->
        <component
            :class="[
                'h-5 w-5',
                onlyLeftIcon ? 'mx-auto' : '', // Centrare dacă doar leftIcon este prezent
                buttonText ? 'mr-2' : '',
                props.loading && 'invisible',
            ]"
            :is="options.leftIcon"
        />

        <!-- Button label -->
        <span :class="[props.loading && 'invisible']">
            <slot />
        </span>

        <!-- Right icon -->
        <component
            :class="[
                'h-5 w-5',
                buttonText ? 'ml-2' : '',
                props.loading && 'invisible',
            ]"
            :is="options.rightIcon"
        />
    </component>
</template>
