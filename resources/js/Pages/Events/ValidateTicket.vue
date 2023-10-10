<script setup>
import { Link } from '@inertiajs/vue3';
import { CameraIcon, TicketIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import Card from '@/Components/UI/Card/Card.vue';
import Button from '@/Components/UI/Button/Button.vue';
import { Input } from '@/Components/UI';

const props = defineProps({
    event: { type: String },
});

const ticketCode = ref('');
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-14">
        <div class="mb-2">
            <Link
                :href="route('events.show', { event: props.event })"
                class="text-blue-500 hover:underline"
                >Back</Link
            >
        </div>

        <!-- Main Container -->
        <div class="bg-white shadow-lg rounded-md p-6 w-full max-w-md mx-auto">
            <!-- Title -->
            <h2 class="text-2xl font-semibold mb-4 text-center">
                Validate Ticket
            </h2>
            <div class="flex flex-col w-full p-4 items-center gap-8">
                <Input
                    v-model="ticketCode"
                    label="Ticket Code"
                    type="text"
                    name="ticket_code"
                    :options="{ borderStyle: 'bordered', leftIcon: TicketIcon }"
                ></Input>
                <div class="flex gap-4">
                    <Button
                        :is="Link"
                        :href="
                            route('ticket_validate.show', {
                                event: props.event,
                                ticket: ticketCode,
                            })
                        "
                        type="button"
                        :options="{
                            leftIcon: TicketIcon,
                        }"
                    >
                        Check Ticket
                    </Button>
                    <Button
                        type="button"
                        :options="{
                            leftIcon: CameraIcon,
                        }"
                    >
                        Scan Ticket
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
