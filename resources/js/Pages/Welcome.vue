<script setup>
import { DateTime, SearchInput, Paginator, Select } from "@/Components/UI";
import EventCard from "@/Components/EventCard.vue";
import { debounce } from "lodash";
import axios from "axios";

import {
    CalendarDaysIcon,
    CheckIcon,
    MagnifyingGlassIcon,
    SquaresPlusIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";

defineProps({
    events: { type: Object },
    links: { type: Object },
});
const searchInputModel = ref("");
const selectInputModel = ref(null);

const allEvents = ref([]);
const allEventTypes = ref([]);

onMounted(() => {
    axios
        .get("/events/all")
        .then((response) => {
            allEvents.value = response.data;
        })
        .catch((error) => {
            console.error(
                "A apărut o eroare la încărcarea evenimentelor",
                error
            );
        });
    axios
        .get("/events/types")
        .then((response) => {
            allEventTypes.value = response.data;
        })
        .catch((error) => {
            console.error(
                "A apărut o eroare la încărcarea evenimentelor",
                error
            );
        });
});

const handleSearch = debounce(() => {
    let params = {};

    if (searchInputModel.value) {
        params.search = searchInputModel.value;
    }

    if (selectInputModel.value) {
        params.type = selectInputModel.value;
    }

    router.get(route("welcome"), params, {
        replace: true,
        preserveState: true,
    });
}, 200);

watch(
    () => searchInputModel.value,
    () => {
        handleSearch();
    }
);

watch(
    () => selectInputModel.value,
    () => {
        handleSearch();
    }
);
</script>

<template>
    <Head title="Welcome" />
    <div class="container bg-gray-100 p-6">
        <div class="relative my-7 w-full">
            <!-- Image -->
            <img
                class="w-full h-40 lg:h-52 object-cover select-none pointer-events-none rounded-md"
                src="/images/login.jpg"
                alt="Login image"
            />
            <!-- Large Screen Text -->
            <div
                class="hidden lg:flex absolute items-center gap-2 justify-center select-none pointer-events-none w-1/2 text-center top-1/4 left-1/2 transform -translate-x-1/2 -translate-y-1/2 font-bold text-xl lg:text-2xl fill-white bg-white bg-opacity-90 px-2 py-1 rounded-md shadow-lg"
            >
                Find an event you will not forget with
                <img
                    class="h-8 lg:h-12"
                    src="/images/logo/logo-no-background.svg"
                    alt="Logo"
                />
            </div>
            <!-- Small Screen Text -->
            <div
                class="lg:hidden flex items-center gap-2 justify-center select-none pointer-events-none text-center font-bold text-lg sm:text-xl fill-white bg-white bg-opacity-90 p-2 rounded shadow-lg"
            >
                Find an event you will not forget with
                <img
                    class="h-6 sm:h-8"
                    src="/images/logo/logo-no-background.svg"
                    alt="Logo"
                />
            </div>
        </div>

        <div
            class="relative items-center justify-center my-8 flex flex-col lg:flex-row gap-2 lg:gap-8 mx-14"
        >
            <SearchInput
                v-model="searchInputModel"
                label="Title"
                type="text"
                name="search"
                :options="{
                    leftIcon: MagnifyingGlassIcon,
                    borderStyle: 'bordered',
                    checkIcon: CheckIcon,
                    xMarkIcon: XMarkIcon,
                    noResultMessage: 'No results found! 😢',
                }"
                :search="{
                    items: allEvents,
                    searchField: 'title',
                    keys: ['title', 'location'],
                    maxResults: 10,
                    hotReload: true,
                }"
                :validityCheck="false"
                :onValidityChange="(newValue) => {}"
            />
            <Select
                v-model="selectInputModel"
                label="Event type"
                :select="allEventTypes"
                selectKey="name"
                :options="{
                    borderStyle: 'bordered',
                    leftIcon: SquaresPlusIcon,
                    dismissable: true,
                }"
            ></Select>
            <DateTime
                label="Date"
                :dateOptions="{
                    minDate: new Date(),
                    maxDate: new Date(2025, 0, 0),
                }"
                :options="{ leftIcon: CalendarDaysIcon }"
            ></DateTime>
        </div>
        <div
            class="my-4 sm:my-6 lg:my-8 flex flex-col items-center gap-4 sm:gap-6 lg:gap-8"
        >
            <div
                class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4"
                v-if="events.data.length > 0"
            >
                <EventCard
                    v-for="event in events.data"
                    :key="event.id"
                    :event="event"
                ></EventCard>
            </div>
            <Paginator :links="links" />
            <div v-if="events.data.length === 0">No events found</div>
        </div>
    </div>
</template>
