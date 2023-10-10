<script setup>
import ShowPasswordIcon from "../Icons/ShowPasswordIcon.vue";
import HidePasswordIcon from "../Icons/HidePasswordIcon.vue";
import { computed, ref, watch, onMounted, onUnmounted } from "vue";
import { cva } from "class-variance-authority";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    type: String,
    name: String,
    error: {
        type: String,
        default: null,
    },
    disabled: Boolean,
    options: {
        type: Object,
        default: () => ({}),
    },
    modelValue: String,
});

const emits = defineEmits(["update:modelValue"]);
const isLabelFloating = ref(false);

const options = computed(() => {
    const defaultOptions = {
        borderStyle: "bordered",
        leftIcon: null,
        passwordIcon: { show: ShowPasswordIcon, hide: HidePasswordIcon },
    };
    return { ...defaultOptions, ...props.options };
});

// Ref for input v-model (for persistent floating label)
const inputTextValue = ref(props.modelValue ? props.modelValue : "");

// Check if input is focused (for floating label)
const isFocused = ref(false);

// Ref for inputType (for Eye Icon)
const inputType = ref(props.type);
const containerElementRef = ref(null);

onMounted(() => {
    document.addEventListener("mousedown", handleOutsideClick);
    if (props.modelValue && props.modelValue !== "") {
        inputTextValue.value = props.modelValue;
        isLabelFloating.value = true;
    }
});

// On component unmount, remove the mousedown event listener.
onUnmounted(() => {
    document.removeEventListener("mousedown", handleOutsideClick);
});

const handleOutsideClick = (event) => {
    if (!containerElementRef.value.contains(event.target)) {
        if (!isFocused.value && inputTextValue.value.trim() === "") {
            isLabelFloating.value = false;
        }
    }
};
watch(
    () => props.modelValue,
    (newValue) => {
        inputTextValue.value = newValue;
        if (newValue === "" && isFocused.value === false) {
            isLabelFloating.value = false;
        }
    }
);

watch(
    () => inputTextValue.value,
    (newValue) => {
        if (newValue === "" && isFocused.value === false) {
            isLabelFloating.value = false;
        }
        emits("update:modelValue", newValue);
    }
);

// Random input id, preventing clashes
const inputId = computed(() => props.name + "-" + crypto.randomUUID());

// A CVA instance for borderStyle variants
const inputClass = computed(() =>
    cva("text-md w-full pl-12 tracking-wider caret-gray-700 outline-none", {
        variants: {
            borderStyle: {
                bordered: "rounded-sm border-2 py-3",
                "border-bottom": "border-b-2 py-3",
                "no-border": "py-3",
            },
            disabled: {
                true: "!cursor-not-allowed !bg-gray-100 !text-gray-400 ",
            },
            password: {
                true: "pr-12",
            },
        },
    })({
        borderStyle: options.value.borderStyle,
        disabled: props.disabled,
        password: props.type === "password",
    })
);

const ariaLabel = computed(() => {
    return (props.name ? props.name : props.type) + " input";
});

const handleInputFocus = () => {
    isFocused.value = true;
    if (inputTextValue.value === "") isLabelFloating.value = true;
};
const handleInputBlur = () => {
    isFocused.value = false;
    if (inputTextValue.value === "") isLabelFloating.value = false;
};

const togglePasswordVisibility = () => {
    inputType.value = inputType.value === "password" ? "text" : "password";
};
</script>

<template>
    <div
        ref="containerElementRef"
        class="relative flex min-h-[2em] w-full flex-col items-center tracking-wider text-gray-700"
    >
        <!-- Left Icon -->
        <div
            class="absolute left-0 ml-2 flex h-full w-8 items-center justify-center"
            aria-hidden="true"
        >
            <component
                class="pointer-events-none absolute left-0 h-8 select-none p-1 text-gray-900"
                :is="options.leftIcon"
            />
        </div>

        <!-- Input -->
        <input
            :class="inputClass"
            :type="inputType"
            :name="props.name"
            :id="inputId"
            v-model="inputTextValue"
            :disabled="props.disabled"
            :aria-label="ariaLabel"
            :aria-invalid="props.error ? true : false"
            :aria-errormessage="props.error ? props.error : null"
            :aria-labelledby="`${inputId}-label`"
            @focus="handleInputFocus"
            @blur="handleInputBlur"
        />

        <!-- Floating Label -->
        <div
            class="pointer-events-none absolute right-0 flex h-full w-full items-center justify-center"
        >
            <span
                class="text-md pointer-events-none absolute left-12 select-none text-gray-700/80 transition-all duration-300"
                :class="
                    isLabelFloating
                        ? '-translate-y-4 transform text-xs'
                        : 'transform-none'
                "
                :id="`${inputId}-label`"
                >{{ props.label }}</span
            >
        </div>

        <!-- Password EyeIcon/EyeSlashIcon -->
        <button
            type="button"
            v-if="props.type === 'password'"
            class="absolute right-0 mr-2 flex h-full w-8 items-center justify-center"
            @click="togglePasswordVisibility"
            :aria-label="
                inputType === 'password' ? 'Show password' : 'Hide password'
            "
        >
            <component
                class="absolute right-0 flex h-8 w-full cursor-pointer select-none items-center justify-center p-1 text-gray-900 transition-all duration-300"
                :is="
                    inputType === 'password'
                        ? options.passwordIcon.show
                        : options.passwordIcon.hide
                "
            />
        </button>

        <!-- Errors -->
        <span
            class="absolute bottom-[-1.75em] left-0 w-full px-1 text-sm font-medium text-red-500"
            :class="[!props.error && 'invisible']"
            >{{ props.error }}</span
        >
    </div>
</template>

<style scoped></style>
