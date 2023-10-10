<script setup>
import { Button, Card, DateTime, Input } from '@/Components/UI';
import {
    ArrowLeftIcon,
    Bars3Icon,
    CalendarDaysIcon,
    CurrencyEuroIcon,
    PlusIcon,
    TicketIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { computed } from 'vue';

const props = defineProps({
    event_id: { type: String },
});

const form = useForm({
    name: '',
    description: '',
    price: '',
    total_tickets: '',
    start_date: new Date(),
    end_date: new Date(),
});
const submit = () => {
    form.post(route('ticket_categories.store', { event: props.event_id }));
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
</script>

<template>
    <Head title="Create ticket category" />
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
                v-model="form.price"
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
                v-model="form.total_tickets"
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
                    :href="route('events.show', { event: event_id })"
                    :options="{ leftIcon: ArrowLeftIcon }"
                >
                    Back
                </Button>
                <Button
                    type="submit"
                    :options="{
                        color: 'red',
                        leftIcon: PlusIcon,
                    }"
                    >Add ticket category</Button
                >
            </div>
        </form>
    </Card>
</template>
