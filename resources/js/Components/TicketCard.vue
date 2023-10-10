<script setup>
import { ClockIcon, TicketIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from './UI/Button/Button.vue';

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);
dayjs.locale('en');

const props = defineProps({ ticket: { type: Object, required: true } });
</script>

<template>
    <Link
        :href="route('tickets.show', { ticket: ticket.uuid })"
        class="group flex flex-col justify-between relative min-h-[20em] overflow-hidden my-2 shadow bg-white rounded-md transition-all sm:hover:scale-100 md:hover:scale-105 hover:z-50 select-none"
    >
        <div
            v-if="props.ticket.validated"
            class="flex items-center justify-center absolute z-10 w-[150%] h-[3em] text-center top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -rotate-45 bg-amber-500/60 text-white py-1 px-4"
        >
            <span class="text-3xl font-extrabold">TICKET USED</span>
        </div>
        <div class="w-full relative z-0">
            <img
                class="w-full h-40 object-cover select-none pointer-events-none rounded-t-md"
                src="https://picsum.photos/1920/1080"
                alt="Event image"
            />
            <div
                class="flex items-center justify-center absolute w-56 top-0 left-0 m-2 rounded-md h-8 bg-white/90 transition-all text-black shadow-sm"
            >
                <div class="text-md text-center font-extrabold align-middle">
                    {{ ticket.ticket_category_name }}
                </div>
            </div>
            <div class="py-4 px-2">
                <h3 class="text-xl">{{ ticket.event_title }}</h3>
                <div class="flex items-center gap-2">
                    <component class="h-6" :is="TicketIcon" />
                    <h3>{{ ticket.name }}</h3>
                </div>
                <div class="flex items-center gap-2">
                    <component class="h-6" :is="ClockIcon" />
                    <h3>
                        Start:
                        {{ dayjs(ticket.start_date).format('DD/MM/YYYY') }} ({{
                            dayjs(ticket.start_date).fromNow()
                        }})
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    <component class="h-6" :is="ClockIcon" />
                    <h3>
                        End:
                        {{ dayjs(ticket.end_date).format('DD/MM/YYYY') }}
                    </h3>
                </div>
            </div>
        </div>
    </Link>
</template>
