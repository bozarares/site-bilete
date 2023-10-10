<script setup>
import CheckIcon from "../Icons/CheckIcon.vue";
import XMarkIcon from "../Icons/XMarkIcon.vue";
import ChevronUpIcon from "../Icons/ChevronUpIcon.vue";
import ChevronDownIcon from "../Icons/ChevronDownIcon.vue";
import { computed, onMounted, onUnmounted, ref, watch, nextTick } from "vue";
import { cva } from "class-variance-authority";
import Fuse from "fuse.js";
import { useFloating } from "@floating-ui/vue";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    type: String,
    name: String,
    search: { type: Object, default: { items: [], keys: [] } },
    options: {
        type: Object,
        default: () => ({}),
    },
    disabled: Boolean,
    modelValue: Array,
});
const emits = defineEmits(["update:modelValue"]);

// Initialize reactive references for various state variables.
const inputTextValue = ref("");
const selectArray = ref(props.modelValue || []);
const inputFieldType = ref(props.type);
const isLabelFloating = ref(false);
const inputElementRef = ref(null);
const searchElementRef = ref(null);
const selectElementRef = ref(null);
const containerElementRef = ref(null);
const isSearchVisible = ref(false);
const isSelectVisible = ref(false);
const fuseSearchInstance = ref(null);

const options = computed(() => {
    const defaultOptions = {
        borderStyle: "bordered",
        leftIcon: null,
        xMarkIcon: XMarkIcon,
        chevronUpIcon: ChevronUpIcon,
        chevronDownIcon: ChevronDownIcon,
        noResultMessage: "No results found",
    };
    return { ...defaultOptions, ...props.options };
});

watch(
    () => selectArray.value,
    (newValue, _1) => {
        emits("update:modelValue", newValue);
    }
);

// Calculate bottom of the element for the search and select using floating-ui
const { floatingStyles: searchFloatingStyles } = useFloating(
    containerElementRef,
    searchElementRef,
    {
        placement: "bottom",
    }
);
const { floatingStyles: selectFloatingStyles } = useFloating(
    containerElementRef,
    selectElementRef,
    {
        placement: "bottom-start",
    }
);

// On search item click add selection to the selectArray
const addSelection = (selection) => {
    selectArray.value.push(selection);
    inputTextValue.value = "";
    inputElementRef.value.focus();
    isLabelFloating.value = true;
};

// On component mount, initialize the Fuse instance and set up a click event listener.
onMounted(() => {
    if (props.search.items.length !== 0) {
        fuseSearchInstance.value = new Fuse(props.search.items, {
            threshold: 0.1,
            includeScore: true,
        });
    }
    document.addEventListener("mousedown", handleOutsideClick);
});

// On component unmount, remove the mousedown event listener.
onUnmounted(() => {
    document.removeEventListener("mousedown", handleOutsideClick);
});

// Function to handle clicks outside of the search element.
const handleOutsideClick = (event) => {
    const searchElement = containerElementRef.value;
    if (!searchElement.contains(event.target)) {
        isSearchVisible.value = false;
        isSelectVisible.value = false;
    }
};

// Watcher for inputTextValue, to update search visibility and input validity.
watch(
    () => inputTextValue.value,
    (newValue, _2) => {
        isSearchVisible.value = true;
        if (newValue !== "") isLabelFloating.value = true;
    }
);

// Global variable for maxResults
const maxResults = props.search.maxResults || 5;

// Function for replacing the charachers "ăâțșî"
const replaceSpecialCharacters = (str) => {
    return str
        .replace(/[ăâ]/g, "a")
        .replace(/[ț]/g, "t")
        .replace(/[ș]/g, "s")
        .replace(/[î]/g, "i");
};

const splitResult = (item, query) => {
    const lowerCaseItemName = replaceSpecialCharacters(item.toLowerCase());
    const lowerCaseQuery = replaceSpecialCharacters(query.toLowerCase());
    const index = lowerCaseItemName.indexOf(lowerCaseQuery);
    if (index === -1) {
        return { beforeMatch: item, match: "", afterMatch: "" };
    }
    const beforeMatch = item.slice(0, index);
    const match = item.slice(index, index + query.length);
    const afterMatch = item.slice(index + query.length);
    return { beforeMatch, match, afterMatch };
};

// Create a map from item to index
const itemToIndex = new Map(
    props.search.items.map((item, index) => [item, index])
);

// Computed property for searchResults
const searchResults = computed(() => {
    if (!fuseSearchInstance) return [];

    // If inputTextValue is empty, return all items up to maxResults
    if (inputTextValue.value === "") {
        let results = props.search.items.slice(
            0,
            maxResults + selectArray.value.length
        );
        return results
            .map((item) => {
                return {
                    item: item,
                    refIndex: itemToIndex.get(item),
                    score: 1,
                    splitResult: splitResult(item, inputTextValue.value),
                };
            })
            .filter((item) => !selectArray.value.includes(item.item));
    }

    // Else perform search using Fuse
    let results = fuseSearchInstance.value.search(inputTextValue.value);

    // If the results length exceeds maxResults, only return up to maxResults
    if (results.length > maxResults) {
        results = results.slice(0, maxResults);
    }

    const resultsWithSplit = results.map((item) => {
        return {
            ...item,
            splitResult: splitResult(item.item, inputTextValue.value),
        };
    });

    const resultsWithoutSelectedItems = resultsWithSplit.filter(
        (item) => !selectArray.value.includes(item.item)
    );
    return resultsWithoutSelectedItems;
});

const isExactMatchFound = computed(() => {
    if (
        searchResults.value[0] &&
        searchResults.value[0].item === inputTextValue.value
    ) {
        return true;
    }
});

// Generate a unique id for the input element.
const uniqueInputId = computed(() => props.name + "-" + crypto.randomUUID());

// Compute classes for the input based on the borderStyle and disabled props.
const inputClass = computed(() =>
    cva("text-md w-full px-12 tracking-wider caret-gray-700 outline-none", {
        variants: {
            borderStyle: {
                bordered: "rounded-sm border-2 py-3",
                "border-bottom": "border-b-2 py-3",
                "no-border": "py-3",
            },
            disabled: {
                true: "!cursor-not-allowed !bg-gray-100 !text-gray-400 ",
            },
        },
    })({ borderStyle: options.value.borderStyle, disabled: props.disabled })
);

// Compute the aria label for the input.
const inputAriaLabel = computed(() => {
    return (props.name ? props.name : props.type) + " input";
});

// Functions to handle focus, blur and reset actions on the input.
const handleInputFocus = () => {
    if (inputTextValue.value === "")
        isLabelFloating.value = !isLabelFloating.value;
    isSearchVisible.value = true;
    // isSelectVisible.value = true;
};
const handleInputBlur = () => {
    if (inputTextValue.value === "")
        isLabelFloating.value = !isLabelFloating.value;
};
const resetInputField = () => {
    inputTextValue.value = "";
    isLabelFloating.value = false;
    isSearchVisible.value = false;
};
const resetInputOnEscape = () => {
    inputTextValue.value = "";
    isLabelFloating.value = true;
    isSearchVisible.value = false;
};
</script>

<template>
    <div
        ref="containerElementRef"
        class="relative flex min-h-[2em] w-full flex-col items-center tracking-wider text-gray-700"
    >
        <!-- Left Icon -->
        <div
            v-if="options.leftIcon"
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
            :class="[inputClass, !options.leftIcon && '!pr-0 pl-6']"
            :type="inputFieldType"
            :name="props.name"
            :id="uniqueInputId"
            v-model="inputTextValue"
            :disabled="props.disabled"
            :aria-label="inputAriaLabel"
            :aria-invalid="props.error ? true : false"
            :aria-errormessage="props.error ? props.error : null"
            :aria-labelledby="`${uniqueInputId}-label`"
            @focus="handleInputFocus"
            @blur="handleInputBlur"
            @keydown.escape="resetInputOnEscape"
            ref="inputElementRef"
        />

        <!-- Floating Label -->
        <div
            class="pointer-events-none absolute right-0 flex h-full w-full items-center justify-center"
        >
            <span
                class="text-md pointer-events-none absolute left-12 select-none text-gray-700/80 transition-all duration-300"
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
                ref="searchElementRef"
                v-if="
                    searchResults &&
                    searchResults != [] &&
                    !isExactMatchFound &&
                    isSearchVisible &&
                    inputTextValue.length
                "
                @mousedown.prevent
                class="absolute left-0 top-0 z-20 flex max-h-[25em] w-full origin-top flex-col items-center overflow-y-auto rounded-b-md border-2 bg-white scrollbar-thin scrollbar-track-gray-100 scrollbar-thumb-gray-400"
                :style="searchFloatingStyles"
            >
                <component
                    v-if="searchResults.length === 0"
                    is="p"
                    class="w-full border-b-2 py-2 text-center"
                >
                    {{ options.noResultMessage }}
                </component>
                <component
                    is="button"
                    class="w-full border-b-2 py-2 text-center"
                    v-else
                    v-for="searchItem in searchResults"
                    :key="searchItem.item.refIndex"
                    @mousedown.prevent
                    @click="addSelection(searchItem.item)"
                >
                    <template v-if="searchItem.splitResult.beforeMatch">
                        {{ searchItem.splitResult.beforeMatch }}
                    </template>
                    <span class="text-blue-500">
                        {{ searchItem.splitResult.match }}
                    </span>
                    <template v-if="searchItem.splitResult.afterMatch">
                        {{ searchItem.splitResult.afterMatch }}
                    </template>
                </component>
            </div>
        </Transition>

        <!-- Clear icon -->
        <TransitionGroup
            enter-from-class="scale-90 opacity-0"
            enter-active-class="transition-all duration-200"
            enter-to-class="scale-100 opacity-100"
            leave-from-class="scale-100 opacity-100"
            leave-active-class="transition-all duration-200"
            leave-to-class="scale-90 opacity-0"
        >
            <button
                v-if="inputTextValue !== ''"
                class="absolute right-0 mr-2 flex h-full w-8 items-center justify-center"
                @click="resetInputField"
                aria-label="Clear input"
            >
                <component
                    class="absolute right-0 h-8 w-8 cursor-pointer select-none p-1 text-gray-900"
                    :is="options.xMarkIcon"
                />
            </button>
        </TransitionGroup>

        <!-- Select List -->
        <div
            v-if="selectArray.length !== 0"
            class="absolute z-10 flex w-full flex-wrap border-2 bg-white"
            :style="selectFloatingStyles"
            ref="selectElementRef"
        >
            <div
                v-if="!isSelectVisible"
                class="relative flex h-6 w-full cursor-pointer items-center justify-center border-b-2"
                @click="isSelectVisible = true"
            >
                <div class="absolute left-0 px-1 text-sm text-gray-500">
                    {{ selectArray.length }} items selected
                </div>
                <component
                    class="h-full stroke-black/20 font-medium"
                    :is="options.chevronDownIcon"
                />
            </div>
            <div
                v-if="isSelectVisible"
                class="m-0.5 flex h-7 grow-0 basis-6 items-center justify-center gap-0.5 rounded-sm bg-gray-700 px-1 text-sm text-white"
                v-for="(item, index) in selectArray"
                :key="item"
            >
                {{ item }}
                <button
                    class="h-auto w-auto stroke-white pl-0.5 text-white"
                    @click="selectArray.splice(index, 1)"
                >
                    <component
                        class="h-5 w-5"
                        :is="options.xMarkIcon"
                    ></component>
                </button>
            </div>
            <div
                v-if="isSelectVisible"
                class="flex h-6 w-full cursor-pointer justify-center border-t-2"
                @click="isSelectVisible = false"
            >
                <component
                    class="h-full stroke-black/20 font-medium"
                    :is="options.chevronUpIcon"
                />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
