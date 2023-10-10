<template>
    <div class="bg-gray-100 min-h-screen mt-8 flex justify-center container">
        <div :class="['p-6', props.class]">
            <div class="bg-white shadow-lg rounded-md p-4 md:p-8 max-w-[50rem]">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({ class: { type: String, default: "" } });
</script>
