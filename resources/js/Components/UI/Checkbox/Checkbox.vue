<script setup>
import { CheckIcon } from "@heroicons/vue/24/outline";
import { cva } from "class-variance-authority";
import { computed } from "vue";

const props = defineProps({
    label: String,
    name: String,
    value: String,
    description: String,
    color: {
        type: String,
        validator: (val) => ["green", "blue", "red", "gray"].includes(val),
        default: "gray",
    },
    border: {
        type: String,
        validator: (val) => ["square", "rounded", "circle"].includes(val),
        default: "rounded",
    },
    checked: Boolean,
    disabled: Boolean,
    modelValue: {
        type: [Boolean, Array],
    },
});

const emits = defineEmits(["update:modelValue"]);

const checkboxClass = computed(() => {
    return cva(
        "mr-2 flex h-6 w-6 items-center justify-center border-2 bg-white duration-150 ease-in-out hover:cursor-pointer hover:transition peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2 peer-focus-visible:transition",
        {
            variants: {
                color: {
                    green: "border-green-700 peer-checked:bg-green-500",
                    blue: "border-blue-700 peer-checked:bg-blue-500",
                    red: "border-red-700 peer-checked:bg-red-500",
                    gray: "border-black peer-checked:bg-gray-700",
                },
                border: {
                    square: "rounded-none",
                    rounded: "rounded-md",
                    circle: "rounded-full",
                },
                disabled: {
                    true: "!hover:ring-0 !cursor-not-allowed !border-gray-300 !bg-gray-100 !text-gray-400 hover:ring-gray-700/0 hover:ring-offset-0 focus:ring-black focus:ring-offset-0",
                },
            },
        }
    )({ color: props.color, border: props.border, disabled: props.disabled });
});
const uniqueInputId = computed(() => crypto.randomUUID());

// Emit the update event according to the type of modelValue
const handleCheck = (event) => {
    if (!Array.isArray(props.modelValue))
        return emits("update:modelValue", event.target.checked);
    let newModelValue = [...props.modelValue];
    if (event.target.checked) {
        newModelValue.push(props.value);
    } else {
        newModelValue = newModelValue.filter((val) => val !== props.value);
    }
    emits("update:modelValue", newModelValue);
};
</script>

<template>
    <div class="p-2">
        <div class="flex items-center">
            <input
                type="checkbox"
                class="peer absolute h-8 w-8 opacity-0"
                :class="[
                    props.disabled ? '!cursor-not-allowed' : 'cursor-pointer',
                ]"
                :id="uniqueInputId"
                :name="props.name"
                :checked="
                    Array.isArray(props.modelValue)
                        ? props.modelValue.includes(props.value)
                        : props.modelValue
                "
                @change="handleCheck"
                :disabled="props.disabled"
            />
            <div :class="checkboxClass">
                <component
                    :class="[props.disabled ? 'text-gray-300' : '']"
                    :is="CheckIcon"
                    class="pointer-events-none hidden h-3/4 w-3/4 stroke-[4] font-bold text-gray-100"
                />
            </div>
            <div
                class="flex flex-col"
                :class="[props.disabled ? 'text-gray-500' : '']"
            >
                <label
                    :for="uniqueInputId"
                    class="select-none"
                    :class="[
                        props.disabled
                            ? '!cursor-not-allowed'
                            : 'cursor-pointer',
                    ]"
                    >{{ props.label }}</label
                >
                <p class="text-sm text-black/60">
                    {{ props.description }}
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
input:checked + div svg {
    @apply block;
}
</style>
