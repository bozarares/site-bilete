<script setup>
import { UserIcon } from "@heroicons/vue/24/outline";
import { cva } from "class-variance-authority";
import { computed } from "vue";
const props = defineProps({
    src: {
        type: String,
        default: null,
    },
    name: {
        type: String,
        default: null,
    },
    acronym: {
        type: String,
        default: null,
    },
    size: {
        type: String,
        validator: (val) => ["small", "normal", "big"].includes(val),
        default: "normal",
    },
    border: {
        type: String,
        validator: (val) => ["square", "rounded", "circle"].includes(val),
        default: "circle",
    },
    as: { type: [String, Object], default: "button" },
    href: String,
});
const getAcronym = computed(() => {
    if (!props.name) return;
    const splitName = props.name.split(" ");
    let acronym = "";

    for (let i = 0; i < splitName.length; i++) {
        if (i < 2) {
            // Only get the first two letters of the first two words
            acronym += splitName[i].charAt(0).toUpperCase();
        }
    }

    return acronym;
});
const avatarClass = computed(() => {
    return cva(
        "flex items-center justify-center overflow-hidden border-2 border-gray-500 bg-gradient-to-br from-gray-300 to-gray-200 font-bold tracking-normal text-gray-700 shadow-sm duration-150 ease-in-out hover:transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:transition",
        {
            variants: {
                size: {
                    small: "min-h-10 min-w-10 h-10 w-10 text-xl",
                    normal: "min-h-12 min-w-12 h-12 w-12 text-2xl",
                    big: "min-h-16 min-w-16 h-16 w-16 text-2xl",
                },
                border: {
                    square: "rounded-none",
                    rounded: "rounded-md",
                    circle: "rounded-full",
                },
            },
        }
    )({ size: props.size, border: props.border });
});
</script>

<template>
    <component :is="props.as" :href="props.href" :class="avatarClass">
        <img
            class="h-full w-full scale-125 object-cover"
            v-if="props.src"
            :src="props.src"
            alt=""
        />
        <div
            v-else-if="props.name"
            class="pointer-events-none select-none leading-10"
        >
            {{ getAcronym }}
        </div>
        <div
            v-else-if="props.acronym"
            class="pointer-events-none select-none leading-10"
        >
            {{ props.acronym }}
        </div>
        <component
            v-else
            class="pointer-events-none w-2/3"
            :is="UserIcon"
        ></component>
    </component>
</template>
