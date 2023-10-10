<script setup>
import { cva } from 'class-variance-authority';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import XMarkIcon from '../Icons/XMarkIcon.vue';

import ChevronDownIcon from '../Icons/ChevronDownIcon.vue';
import { useFloating } from '@floating-ui/vue';

const props = defineProps({
    name: String,
    label: String,
    disabled: Boolean,
    options: {
        type: Object,
        default: () => ({}),
    },
    select: Array,
    selectKey: String,
    retrieveKey: String,
    retrieveKeyCallback: {
        type: Function,
        default: () => {},
    },
    modelValue: [String, Number],
});

const emits = defineEmits(['update:modelValue']);

const selectValue = ref(props.modelValue ? props.modelValue : '');
const selectElementRef = ref(null);
const selectItemRef = ref(null);
const isSelectVisible = ref(false);
const containerElementRef = ref(null);

const options = computed(() => {
    const defaultOptions = {
        borderStyle: 'bordered',
        leftIcon: null,
        rightIcon: ChevronDownIcon,
        xMarkIcon: XMarkIcon,
        dismissable: true,
    };
    return { ...defaultOptions, ...props.options };
});

// Determine the bottom position for the select list
const { floatingStyles: selectFloatingStyles } = useFloating(
    containerElementRef,
    selectElementRef,
    {
        placement: 'bottom',
    },
);

// Watcher for updating the v-model and hiding the select list
watch(
    () => selectValue.value,
    (newValue) => {
        if (props.retrieveKey)
            props.retrieveKeyCallback(selectItemRef.value[props.retrieveKey]);
        emits('update:modelValue', newValue);
        isSelectVisible.value = false;
    },
);

// The floating label will be raised if selectValue is not empty
const isLabelFloating = computed(() => {
    if (selectValue.value === '') return false;
    return true;
});

// On component mount, add the mousedown event listener.
onMounted(() => {
    document.addEventListener('mousedown', handleOutsideClick);
});

// On component unmount, remove the mousedown event listener.
onUnmounted(() => {
    document.removeEventListener('mousedown', handleOutsideClick);
});

// Function for handling the outside click, if the click is not inside the container
const handleOutsideClick = (event) => {
    const selectElement = containerElementRef.value;
    if (!selectElement.contains(event.target)) {
        isSelectVisible.value = false;
    }
};

// Toggle function for select list
const toggleIsSelectVisible = () => {
    isSelectVisible.value = !isSelectVisible.value;
};

// CVA instance for design options
const inputClass = computed(() => {
    return cva(
        'text-md bg-white h-12 w-full pl-12 pr-6 tracking-wider caret-gray-700 outline-none',
        {
            variants: {
                borderStyle: {
                    bordered: 'rounded-sm border-2 border-gray-500 py-3',
                    'border-bottom': 'border-b-2 py-3',
                    'no-border': 'py-3',
                },
                disabled: {
                    true: '!cursor-not-allowed !bg-gray-100 !text-gray-400 ',
                },
            },
        },
    )({ borderStyle: options.value.borderStyle, disabled: props.disabled });
});
// Generate a unique id for the input element.
const uniqueInputId = computed(() => props.name + '-' + crypto.randomUUID());
</script>
<template>
    <div
        ref="containerElementRef"
        :class="props.disabled ? 'pointer-events-none' : ''"
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
        <!-- Right Icon -->
        <div
            class="absolute right-0 mr-2 flex h-full w-8 items-center justify-center"
            aria-hidden="true"
            @click="toggleIsSelectVisible"
        >
            <component
                class="absolute left-0 cursor-pointer select-none text-gray-900"
                :is="options.rightIcon"
            />
        </div>
        <!-- Floating Label -->
        <div
            class="pointer-events-none absolute right-0 flex h-full w-full items-center justify-center"
        >
            <span
                class="text-md pointer-events-none absolute left-12 select-none text-gray-700/80 transition-all duration-100"
                :class="[
                    isLabelFloating
                        ? '-translate-y-4 transform text-xs'
                        : 'transform-none',
                    !options.leftIcon && '!left-6',
                ]"
                :id="`${uniqueInputId}-label`"
                >{{ props.label }}</span
            >
        </div>

        <!-- Hidden real select -->
        <select
            v-model="selectValue"
            class="pointer-events-none absolute h-1 w-1 opacity-0"
            @change="
                (e) => {
                    selectValue = e.target.value;
                }
            "
            :name="props.name"
        >
            <option v-if="options.dismissable" value="">None</option>
            <option
                v-for="selectItem in props.select"
                :key="selectKey ? selectItem[props.selectKey] : selectItem"
                :value="selectKey ? selectItem[props.selectKey] : selectItem"
            >
                {{ selectKey ? selectItem[props.selectKey] : selectItem }}
            </option>
        </select>

        <!-- Custom Selection -->
        <div
            :class="[
                inputClass,
                !options.leftIcon && '!pl-6',
                !options.rightIcon && '!pr-6',
            ]"
            @click="isSelectVisible = true"
        >
            {{ selectValue }}
        </div>

        <!-- Search Result -->
        <Transition
            enter-from-class="scale-90 opacity-0"
            enter-active-class="transition duration-200"
            enter-to-class="scale-100 opacity-100"
            leave-from-class="scale-100 opacity-100"
            leave-active-class="transition duration-200"
            leave-to-class="scale-90 opacity-0"
            mode="out-in"
        >
            <div
                ref="selectElementRef"
                v-if="props.select && isSelectVisible"
                @mousedown.prevent
                class="absolute left-0 top-0 z-20 flex max-h-[25em] w-full origin-top flex-col items-center overflow-y-auto rounded-b-md border-2 bg-white scrollbar-thin scrollbar-track-gray-100 scrollbar-thumb-gray-400"
                :style="selectFloatingStyles"
            >
                <component
                    v-if="options.dismissable"
                    is="button"
                    class="w-full border-b-2 py-2 text-center"
                    @mousedown.prevent
                    @click.prevent
                    @click="selectValue = ''"
                >
                    <div>None</div>
                </component>

                <component
                    is="button"
                    class="w-full border-b-2 py-2 text-center"
                    v-for="selectItem in props.select"
                    :key="selectKey ? selectItem[props.selectKey] : selectItem"
                    @mousedown.prevent
                    @click.prevent
                    @click="
                        () => {
                            selectValue = selectKey
                                ? selectItem[props.selectKey]
                                : selectItem;
                            selectItemRef = selectItem;
                        }
                    "
                >
                    <div>
                        {{
                            selectKey ? selectItem[props.selectKey] : selectItem
                        }}
                    </div>
                </component>
            </div>
        </Transition>
    </div>
</template>
