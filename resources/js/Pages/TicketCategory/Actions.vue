<script setup>
import { Button, Card } from '@/Components/UI';
import {
    ArrowLeftIcon,
    PauseIcon,
    PencilSquareIcon,
    PlayIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import axiosInstance from '@/axiosInstance';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { useToastStore } from '@/store/toastStore';
const toastStore = useToastStore();

dayjs.extend(relativeTime);
dayjs.locale('en');

const page = usePage();
const props = defineProps({
    event_id: { type: String },
    ticket_category: { type: Object, default: () => {} },
});

const paused = ref(props.ticket_category.paused);

const toggleTicketCategory = () => {
    axiosInstance
        .post(
            route('ticket_category.toggle', {
                event: props.event_id,
                ticket_category: props.ticket_category.id,
            }),
        )
        .then((response) => {
            if (response.data && response.data.message) {
                toastStore.addToast({
                    title: 'Ticket Category Notification',
                    message: response.data.message,
                    status: 'success',
                });
                paused.value = !paused.value;
            }
        })
        .catch((error) => {
            toastStore.addToast({
                title: 'Ticket Category Notification',
                status: 'warning',
                message: error.message,
            });
        });
};

const message = computed(() => page.props.message);
</script>

<template>
    <Head title="Actions" />

    <Card>
        <div
            class="w-full p-4 text-center text-2xl font-semibold mb-4 rounded-md"
            :class="{
                'bg-green-300':
                    !paused &&
                    props.ticket_category.bought_tickets !==
                        props.ticket_category.total_tickets,
                'bg-red-300': paused,
                'bg-amber-300':
                    props.ticket_category.bought_tickets ===
                    props.ticket_category.total_tickets,
            }"
        >
            {{
                paused
                    ? 'Paused Selling'
                    : props.ticket_category.bought_tickets ===
                      props.ticket_category.total_tickets
                    ? 'All Tickets Sold'
                    : 'Active Selling'
            }}
        </div>

        <div class="flex flex-col gap-2">
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Name</span
                >
                <span class="text-sm font-semibold text-gray-700">{{
                    props.ticket_category.name
                }}</span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Description</span
                >
                <span class="text-sm font-semibold text-gray-700">{{
                    props.ticket_category.description
                }}</span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Price</span
                >
                <span class="text-sm font-semibold text-gray-700">{{
                    props.ticket_category.price
                }}</span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Total number of tickets</span
                >
                <span class="text-sm font-semibold text-gray-700">{{
                    props.ticket_category.total_tickets
                }}</span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Total number of bought tikets</span
                >
                <span class="text-sm font-semibold text-gray-700">{{
                    props.ticket_category.bought_tickets
                }}</span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Total number of remaining tickets</span
                >
                <span class="text-sm font-semibold text-gray-700">{{
                    props.ticket_category.remaining_tickets
                }}</span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Total revenue generated by category</span
                >
                <span class="text-sm font-semibold text-gray-700"
                    >${{ props.ticket_category.category_revenue }}</span
                >
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >Start date</span
                >
                <span class="text-sm font-semibold text-gray-700">
                    {{
                        dayjs(props.ticket_category.start_date).format(
                            'DD/MM/YYYY HH:mm',
                        )
                    }}
                </span>
            </p>
            <p class="flex flex-col sm:flex-row items-center sm:gap-4">
                <span class="text-md font-semibold text-center text-black"
                    >End date</span
                >
                <span class="text-sm font-semibold text-gray-700">
                    {{
                        dayjs(props.ticket_category.end_date).format(
                            'DD/MM/YYYY HH:mm',
                        )
                    }}
                </span>
            </p>
        </div>
        <div class="flex flex-col my-4 gap-4">
            <h2 class="text-center text-xl font-bold">Actions</h2>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <Button
                    :is="Link"
                    :href="
                        route('ticket_category.edit', {
                            event: props.event_id,
                            ticket_category: props.ticket_category,
                        })
                    "
                    :options="{
                        color: 'amber',
                        leftIcon: PencilSquareIcon,
                    }"
                    >Edit</Button
                >
                <Button
                    as="button"
                    @click="
                        () => {
                            toggleTicketCategory();
                        }
                    "
                    :options="{
                        leftIcon: paused === true ? PlayIcon : PauseIcon,
                    }"
                    >{{ paused == false ? 'Pause' : 'Resume' }} Sell</Button
                >
                <!-- <div v-if="message">
                    {{ message }}
                </div> -->
                <Button
                    :is="Link"
                    :disabled="props.ticket_category.bought_tickets !== 0"
                    as="button"
                    method="DELETE"
                    :href="
                        route('ticket_category.destroy', {
                            event: props.event_id,
                            ticket_category: props.ticket_category.id,
                        })
                    "
                    :options="{
                        color: 'red',
                        leftIcon: TrashIcon,
                    }"
                    >Delete</Button
                >
            </div>
            <p
                class="text-red-500 text-justify"
                v-if="props.ticket_category.bought_tickets !== 0"
            >
                A ticket from this category has already been purchased. You
                cannot delete the category, but you can pause its sales.
            </p>
        </div>
        <div class="flex justify-center w-full">
            <Button
                :is="Link"
                :href="route('events.show', { event: props.event_id })"
                :options="{ leftIcon: ArrowLeftIcon }"
            >
                Back
            </Button>
        </div>
    </Card>
</template>
