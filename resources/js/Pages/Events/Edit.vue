<script setup>
import { Button, Card, DateTime, Input, Select } from '@/Components/UI';
import {
    ArrowLeftIcon,
    Bars3Icon,
    CalendarDaysIcon,
    MapPinIcon,
    PencilSquareIcon,
    SquaresPlusIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { computed } from 'vue';
import { ref } from 'vue';

const props = defineProps({
    event: { type: Object },
    auth: { type: Object },
    types: { type: Array },
});

const selectedEventType = ref(props.event.event_type.name);

const form = useForm({
    user_uuid: props.auth.user.uuid,
    title: props.event.title,
    location: props.event.location,
    description: props.event.description,
    event_type_id: props.event.event_type.id,
    start_date: props.event.start_date,
    end_date: props.event.end_date,
});
const submit = () => {
    form.patch(route('events.update', { event: props.event.id }));
};

const updateEventType = (id) => {
    form.event_type_id = id;
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
    <Head title="Edit event" />
    <Card>
        <form
            @submit.prevent="submit"
            class="flex flex-col w-full p-4 items-center gap-8"
        >
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <Input
                v-model="form.title"
                label="Title"
                type="text"
                name="title"
                :error="form.errors.title"
                :options="{ borderStyle: 'bordered', leftIcon: Bars3Icon }"
            >
            </Input>
            <Input
                v-model="form.location"
                label="Location"
                type="text"
                name="location"
                :error="form.errors.location"
                :options="{ borderStyle: 'bordered', leftIcon: MapPinIcon }"
            >
            </Input>
            <Input
                v-model="form.description"
                label="Description"
                type="text"
                name="description"
                :error="form.errors.description"
                :options="{ borderStyle: 'bordered', leftIcon: Bars3Icon }"
            >
            </Input>
            <Select
                label="Event type"
                v-model="selectedEventType"
                :select="props.types"
                selectKey="name"
                retrieveKey="id"
                :retrieveKeyCallback="updateEventType"
                :options="{
                    borderStyle: 'bordered',
                    leftIcon: SquaresPlusIcon,
                    dismissable: false,
                }"
            ></Select>
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
                    :href="route('events.show', { event: props.event.id })"
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
                    >Edit Event</Button
                >
            </div>
        </form>
    </Card>
</template>
