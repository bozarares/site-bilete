<script setup>
import EventCard from '@/Components/EventCard.vue';
import { Button } from '@/Components/UI';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: { type: Array },
    info: { type: Object, default: () => ({}) },
});
</script>
<template>
    <Head :title="props.info.title ? props.info.title : 'Events'" />
    <div class="bg-gray-100 min-h-screen p-6 mx-4 xl:mx-32 my-8">
        <div class="mb-4">
            <Button
                :href="route('dashboard')"
                :is="Link"
                as="button"
                :options="{ leftIcon: ArrowLeftIcon }"
                >Dashboard</Button
            >
        </div>
        <div class="flex flex-col items-center gap-4">
            <div
                class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4"
                v-if="events.length > 0"
            >
                <EventCard
                    v-for="event in events"
                    :key="event.id"
                    :event="event"
                ></EventCard>
            </div>
            <!-- <Paginator :links="links" /> -->
            <div v-if="events.length === 0">No events found</div>
        </div>
    </div>
</template>
