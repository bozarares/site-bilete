<script setup>
import { ClockIcon } from '@heroicons/vue/24/outline';
import { MapPinIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);
dayjs.locale('en');

const props = defineProps({ event: { type: Object } });
</script>

<template>
    <Link
        class="group flex flex-col justify-between relative min-h-[20em] my-2 shadow bg-white rounded-md transition-all sm:hover:scale-100 md:hover:scale-105 hover:z-50 select-none"
        :href="route('events.show', { event: event.id })"
    >
        <div class="flex flex-col">
            <div class="w-full relative">
                <img
                    class="w-full h-40 object-cover select-none pointer-events-none rounded-t-md"
                    src="https://picsum.photos/1920/1080"
                    alt="Login image"
                />
                <div
                    class="flex items-center justify-center absolute w-24 top-0 left-0 m-2 rounded-md h-8 bg-white/90 transition-all text-black shadow-sm"
                >
                    <div
                        class="text-md text-center font-extrabold align-middle"
                    >
                        {{ event.event_type.name }}
                    </div>
                </div>
            </div>
            <div class="py-4 px-2 flex flex-col gap-1">
                <h3 class="text-xl">{{ props.event.title }}</h3>
                <div class="flex items-center gap-2">
                    <component class="h-6" :is="MapPinIcon" />
                    <h3>{{ props.event.location }}</h3>
                </div>
                <div class="flex items-center gap-2">
                    <component class="h-6" :is="ClockIcon" />
                    <h3>
                        Start:
                        {{ dayjs(props.event.start_date).format('DD/MM/YYYY') }}
                        ({{ dayjs(props.event.start_date).fromNow() }})
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    <component class="h-6" :is="ClockIcon" />
                    <h3>
                        End:
                        {{ dayjs(props.event.end_date).format('DD/MM/YYYY') }}
                    </h3>
                </div>
            </div>
        </div>
    </Link>
</template>
