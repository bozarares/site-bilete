<script setup>
import { Button, Card, DateTime, Input } from '@/Components/UI';
import {
    ArrowLeftIcon,
    Bars3Icon,
    CalendarDaysIcon,
    CurrencyEuroIcon,
    PencilSquareIcon,
    TicketIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
    ticket_category: { type: Object },
    event_id: { type: String },
});

const form = useForm({
    name: props.ticket_category.name,
    description: props.ticket_category.description,
    price: props.ticket_category.price,
    total_tickets: props.ticket_category.total_tickets,
    start_date: props.ticket_category.start_date,
    end_date: props.ticket_category.end_date,
});
const submit = () => {
    form.patch(
        route('ticket_category.update', {
            event: props.event_id,
            ticket_category: props.ticket_category.id,
        }),
    );
};

function formatDateToMySQL(dateObj) {
    const yyyy = dateObj.getFullYear();
    const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
    const dd = String(dateObj.getDate()).padStart(2, '0');
    const hh = String(dateObj.getHours()).padStart(2, '0');
    const mi = String(dateObj.getMinutes()).padStart(2, '0');
    const ss = String(dateObj.getSeconds()).padStart(2, '0');

    return `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
}

const startDateComputed = computed({
    get() {
        return dayjs(form.start_date).toDate();
    },
    set(value) {
        form.start_date = formatDateToMySQL(dayjs(value).toDate());
    },
});

const endDateComputed = computed({
    get() {
        return dayjs(form.end_date).toDate();
    },
    set(value) {
        form.end_date = formatDateToMySQL(dayjs(value).toDate());
    },
});

const totalTicketsAsString = computed({
    get() {
        return form.total_tickets.toString();
    },
    set(value) {
        form.total_tickets = parseInt(value, 10);
    },
});
const totalPriceAsString = computed({
    get() {
        return form.price.toString();
    },
    set(value) {
        form.price = parseInt(value, 10);
    },
});
</script>

<template>
    <Head title="Edit ticket category" />
    <Card>
        <form
            @submit.prevent="submit"
            class="flex flex-col w-full p-4 items-center gap-8"
        >
            <Input
                v-model="form.name"
                label="Name"
                type="text"
                name="name"
                :error="form.errors.name"
                :options="{ borderStyle: 'bordered', leftIcon: Bars3Icon }"
            />
            <Input
                v-model="form.description"
                label="Description"
                type="text"
                name="description"
                :error="form.errors.description"
                :options="{ borderStyle: 'bordered', leftIcon: Bars3Icon }"
            />
            <Input
                v-model="totalPriceAsString"
                label="Price"
                type="text"
                name="price"
                :error="form.errors.price"
                :options="{
                    borderStyle: 'bordered',
                    leftIcon: CurrencyEuroIcon,
                }"
            />
            <Input
                v-model="totalTicketsAsString"
                label="Total tickets"
                type="text"
                name="total_tickets"
                :error="form.errors.total_tickets"
                :options="{ borderStyle: 'bordered', leftIcon: TicketIcon }"
            />

            <DateTime
                label="Start Date"
                v-model="startDateComputed"
                :dateOptions="{
                    time: true,
                    minDate: new Date(),
                    maxDate: new Date(2025, 0, 0),
                }"
                :error="form.errors.start_date"
                :options="{ leftIcon: CalendarDaysIcon }"
            ></DateTime>
            <DateTime
                label="End Date"
                v-model="endDateComputed"
                :dateOptions="{
                    time: true,
                    minDate: new Date(),
                    maxDate: new Date(2025, 0, 0),
                }"
                :error="form.errors.end_date"
                :options="{ leftIcon: CalendarDaysIcon }"
            ></DateTime>
            <div class="flex items-center gap-4">
                <Button
                    :is="Link"
                    :href="
                        route('ticket_category.actions', {
                            event: props.event_id,
                            ticket_category: ticket_category.id,
                        })
                    "
                    :options="{ leftIcon: ArrowLeftIcon }"
                >
                    Back
                </Button>
                <Button
                    type="submit"
                    :options="{
                        color: 'red',
                        leftIcon: PencilSquareIcon,
                    }"
                    >Edit ticket category</Button
                >
            </div>
        </form>
    </Card>
</template>
