<script setup>
import { ref, onMounted } from 'vue';
import QRCode from 'qrcode';
import { Head, Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button/Button.vue';
import dayjs from 'dayjs';

const props = defineProps({
    ticket: { type: Object, default: () => ({}) },
});

const qrCode = ref(''); // Set initial qrCode as empty string

// Function to generate QR code
async function generateQRCode() {
    try {
        qrCode.value = await QRCode.toDataURL(`${props.ticket.uuid}`, {
            errorCorrectionLevel: 'L',
        });
    } catch (err) {
        console.error(err);
    }
}
onMounted(generateQRCode);
</script>

<template>
    <Head title="Ticket" />

    <div class="bg-gray-100 min-h-screen p-8 overflow-hidden">
        <div class="mb-4">
            <Link
                :href="route('tickets.index')"
                class="text-blue-500 hover:underline"
                >Back</Link
            >
        </div>
        <div
            class="relative bg-white p-6 rounded-md shadow-md max-w-md mx-auto overflow-hidden"
        >
            <div
                v-if="props.ticket.validated"
                class="flex items-center justify-center absolute z-10 w-[200%] h-[3em] text-center top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -rotate-45 bg-amber-500/60 text-white py-1 px-4"
            >
                <span class="text-3xl font-extrabold">TICKET USED</span>
            </div>
            <h2 class="text-2xl font-semibold mb-4">
                {{ props.ticket.ticket_category_name }}
            </h2>
            <p class="mb-2">
                <strong>Event:</strong> {{ props.ticket.event_title }}
            </p>
            <p class="mb-2">
                <strong>Location:</strong> {{ props.ticket.event_location }}
            </p>
            <p class="mb-2">
                <strong>Start Date:</strong>
                {{ dayjs(props.start_date).format('DD/MM/YYYY HH:mm') }}
            </p>
            <p class="mb-2">
                <strong>End Date:</strong>
                {{ dayjs(props.end_date).format('DD/MM/YYYY HH:mm') }}
            </p>
            <p class="mb-2">
                <strong>Price:</strong> ${{ props.ticket.price }}
            </p>
            <p class="mb-4"><strong>Name:</strong> {{ props.ticket.name }}</p>

            <Button
                :is="Link"
                v-if="!props.ticket.validated"
                :href="route('tickets.edit', { ticket: props.ticket.uuid })"
                class="text-sm"
                >Edit Name</Button
            >

            <div class="mt-6">
                <img :src="qrCode" alt="QR code" class="mx-auto" />
            </div>
        </div>
    </div>
</template>
