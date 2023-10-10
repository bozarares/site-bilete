<template>
    <component
        :method="props.method"
        :as="props.as"
        :is="props.is"
        :href="props.href"
        :class="dropdownItemClass"
    >
        <component
            class="absolute left-0 mr-2 h-6 pl-3 text-gray-700/60"
            :is="leftIcon"
        />
        <div class="inline-block w-full text-center">
            <slot />
        </div>
        <component
            class="absolute right-0 ml-2 h-6 pr-3 text-gray-700/60"
            :is="rightIcon"
        />
    </component>
</template>

<script setup>
import { cva } from "class-variance-authority";
import { computed } from "vue";

const props = defineProps({
    leftIcon: {
        type: [Object, Function],
    },
    rightIcon: {
        type: [Object, Function],
    },
    is: {
        type: [String, Object],
        default: "button",
    },
    as: {
        type: [String],
        default: "button",
    },
    method: { type: String },
    size: {
        type: String,
        default: "normal",
        validator: (val) => ["normal", "xl", "xxl"].includes(val),
    },
    href: String,
});

const dropdownItemClass = computed(() => {
    return cva(
        "relative flex w-full cursor-pointer select-none items-center tracking-wider transition-colors hover:bg-gray-200",
        {
            variants: {
                size: { normal: "h-[2.5em]", xl: "h-[3em]", xxl: "h-[4em]" },
            },
        }
    )({ size: props.size });
});
</script>
