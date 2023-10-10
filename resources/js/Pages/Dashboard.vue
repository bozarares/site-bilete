<script setup>
import { Card } from "@/Components/UI";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    events: { type: Array },
    staffedEvents: { type: Array },
    tickets: { type: Array },
});
</script>

<template>
    <Head title="Dashboard" />
    <Card>
        <h1 class="text-2xl font-semibold mb-4">
            {{ $page.props.auth.user.name }}
        </h1>
        <div class="flex flex-col md:flex-row gap-3">
            <Link
                :href="route('events.index', { show: 'myevents' })"
                class="flex-1 text-center py-2 px-4 rounded bg-gray-800 hover:bg-gray-600 text-white shadow flex flex-col justify-center items-center"
            >
                <span class="font-bold">Events organised</span>
                <span class="text-3xl font-bold">{{
                    props.events.length
                }}</span>
            </Link>
            <Link
                :href="route('events.index', { show: 'mystaffed' })"
                class="flex-1 text-center py-2 px-4 rounded bg-gray-800 hover:bg-gray-600 text-white shadow flex flex-col justify-center items-center"
            >
                <span class="font-bold">Events staffed</span>
                <span class="text-3xl font-bold">{{
                    props.staffedEvents.length
                }}</span>
            </Link>
            <Link
                :href="route('tickets.index')"
                class="flex-1 text-center py-2 px-4 rounded bg-gray-800 hover:bg-gray-600 text-white flex flex-col justify-center items-center"
            >
                <span class="font-bold">My tickets</span>
                <span class="text-3xl font-bold">{{
                    props.tickets.length
                }}</span>
            </Link>
        </div>
    </Card>
</template>
