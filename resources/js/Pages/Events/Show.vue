<script setup>
import { Button, TicketCategoryCard, StaffCard } from '@/Components/UI';

import {
    PencilSquareIcon,
    PlusIcon,
    TicketIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);
dayjs.locale('en');

const props = defineProps({
    event: { type: Object },
});

const staffList = ref(props.event.staff);
const handleStaffDeleted = (staffId) => {
    staffList.value = staffList.value.filter((staff) => staff.id !== staffId);
};

const editPermissions = computed(() => {
    if (props.event.is_organising || props.event.can_edit) return true;
    return false;
});

const urlPrev = usePage().props.urlPrev;

const back = () => {
    router.visit(urlPrev);
};
</script>
<template>
    <Head :title="props.event.title" />
    <div class="bg-gray-100 min-h-screen my-8 md:w-[50em] w-full">
        <div class="container mx-auto p-6">
            <button class="text-blue-500 hover:underline mb-2" @click="back">
                Back
            </button>
            <!-- <div class="mb-6 text-right">
                <Link
                    :href="route('events.index')"
                    class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600"
                    >Back to Events</Link
                >
            </div> -->

            <div class="bg-white shadow-lg rounded-md relative">
                <img
                    class="w-full rounded-t-md h-40 object-cover select-none pointer-events-none"
                    src="https://picsum.photos/1920/1080"
                    alt="Login image"
                />
                <div v-if="editPermissions" class="absolute top-4 right-4">
                    <Button
                        :is="Link"
                        :href="route('events.edit', { event: props.event.id })"
                        :options="{
                            color: 'amber',
                            leftIcon: PencilSquareIcon,
                        }"
                    >
                        Edit
                    </Button>
                </div>
                <div v-if="event.can_validate" class="absolute left-4 top-4">
                    <Button
                        :is="Link"
                        :href="
                            route('event.validate', { event: props.event.id })
                        "
                        :options="{
                            color: 'green',
                            leftIcon: TicketIcon,
                        }"
                    >
                        Validate Tickets
                    </Button>
                </div>
                <div class="p-6">
                    <h1 class="text-2xl font-semibold mb-4">
                        {{ props.event.title }}
                    </h1>
                    <div class="flex flex-col gap-4">
                        <p><strong>ID:</strong> {{ props.event.id }}</p>

                        <p>
                            <strong>Description:</strong>
                            {{ props.event.description }}
                        </p>
                        <p>
                            <strong>Location:</strong>
                            {{ props.event.location }}
                        </p>
                        <p>
                            <strong>Start Date:</strong>
                            {{
                                dayjs(props.event.start_date).format(
                                    'DD/MM/YYYY HH:mm',
                                )
                            }}
                            ({{ dayjs(props.event.start_date).fromNow() }})
                        </p>
                        <p>
                            <strong>End Date:</strong>
                            {{
                                dayjs(props.event.end_date).format(
                                    'DD/MM/YYYY HH:mm',
                                )
                            }}
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col gap-2">
                        <h2 class="text-xl font-semibold mb-4">
                            Ticket Categories
                        </h2>
                        <Button
                            v-if="editPermissions"
                            :is="Link"
                            :href="
                                route('ticket_categories.create', {
                                    event: props.event.id,
                                })
                            "
                            :options="{
                                leftIcon: PlusIcon,
                            }"
                            >Add Ticket Category</Button
                        >
                        <p v-if="props.event.ticket_categories.length === 0">
                            No categories found
                        </p>
                        <div class="flex flex-col gap-4">
                            <TicketCategoryCard
                                v-for="ticket_category in props.event
                                    .ticket_categories"
                                :key="ticket_category.id"
                                :ticketCategory="ticket_category"
                                :eventId="props.event.id"
                                :editPermissions="editPermissions"
                            />
                        </div>
                    </div>
                    <div
                        v-if="editPermissions"
                        class="mt-6 flex flex-col gap-2"
                    >
                        <h2 class="text-xl font-semibold mb-4">Staff</h2>
                        <Button
                            :is="Link"
                            :href="
                                route('staff.create', {
                                    event: props.event.id,
                                })
                            "
                            :options="{
                                leftIcon: PlusIcon,
                            }"
                            >Add Staff Member</Button
                        >
                        <div v-if="staffList.length === 0">
                            No staff available
                        </div>
                        <div v-else>
                            <StaffCard
                                class="flex gap-4"
                                v-for="staff in staffList"
                                :key="staff.id"
                                :staff="staff"
                                :event_id="props.event.id"
                                :handleStaffDelete="handleStaffDeleted"
                            >
                            </StaffCard>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
