<script setup>
import { computed, ref } from 'vue';
import TicketCard from '@/Components/TicketCard.vue';
import { Button, Checkbox } from '@/Components/UI';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    tickets: { type: Array, default: () => [] },
});

const showUsedTickets = ref(false);

const filteredTickets = computed(() => {
    return showUsedTickets.value
        ? props.tickets
        : props.tickets.filter((ticket) => !ticket.validated);
});

const groupedTickets = computed(() => {
    return filteredTickets.value.reduce((grouped, ticket) => {
        const eventName = ticket.event_title;
        if (!grouped[eventName]) {
            grouped[eventName] = [];
        }
        grouped[eventName].push(ticket);
        return grouped;
    }, {});
});
</script>

<template>
    <Head title="My Tickets" />

    <div class="bg-gray-100 min-h-screen min-w-[90%] p-6 mx-4 xl:mx-32 my-8">
        <div class="mb-4 flex flex-col items-start">
            <Button
                :href="route('dashboard')"
                :is="Link"
                as="button"
                :options="{ leftIcon: ArrowLeftIcon }"
                class="mb-2"
                >Dashboard</Button
            >
            <Checkbox
                label="Show used tickets"
                color="gray"
                v-model="showUsedTickets"
            />
        </div>
        <div v-if="Object.keys(groupedTickets).length === 0" class="text-left">
            No tickets found!
        </div>
        <div v-else>
            <div
                v-for="(group, eventName) in groupedTickets"
                :key="eventName"
                class="my-4"
            >
                <h2 class="text-lg font-bold mb-2 text-left">
                    {{ eventName }}
                </h2>
                <div
                    class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4"
                >
                    <TicketCard
                        v-for="ticket in group"
                        :key="ticket.uuid"
                        :ticket="ticket"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
