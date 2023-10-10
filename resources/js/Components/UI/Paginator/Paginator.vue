<template>
    <nav class="flex items-center justify-between gap-4">
        <span
            v-if="props.links.current !== 1"
            @click="navigate(props.links.prev)"
            class="cursor-pointer text-blue-500"
            >First</span
        >
        <span v-else class="text-gray-500">First</span>
        <span
            v-if="props.links.current !== 1"
            @click="navigate(props.links.first)"
            class="cursor-pointer text-blue-500"
            >Previous</span
        >
        <span v-else class="text-gray-500">Previous</span>

        <span>
            Page {{ props.links.current }} of
            {{ props.links.total }}
        </span>

        <span
            v-if="props.links.current !== props.links.total"
            @click="navigate(props.links.next)"
            class="cursor-pointer text-blue-500"
            >Next</span
        >
        <span v-else class="text-gray-500">Next</span>
        <span
            v-if="props.links.current !== props.links.total"
            @click="navigate(props.links.last)"
            class="cursor-pointer text-blue-500"
            >Last</span
        >
        <span v-else class="text-gray-500">Last</span>
    </nav>
</template>

<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    links: Object,
});

const navigate = (url) => {
    router.visit(url);
};
</script>
