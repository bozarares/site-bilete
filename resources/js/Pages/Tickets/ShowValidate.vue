<script setup>
import { Link, Head } from '@inertiajs/vue3';
const props = defineProps({
    event_id: { type: String },
    ticket: { type: Object, default: () => {} },
});
console.log(props.event_id, props.ticket);
</script>

<template>
    <Head title="Validate ticket" />

    <!-- Main Container -->
    <div class="min-h-screen bg-gray-100 p-8">
        <!-- Card -->
        <div class="mb-4">
            <Link
                :href="route('event.validate', { event: props.event_id })"
                class="text-blue-500 hover:underline"
                >Back</Link
            >
        </div>
        <div class="bg-white shadow-lg rounded-md p-6 w-full max-w-lg">
            <!-- Title -->
            <h2 class="text-2xl font-semibold mb-4 text-center">
                Validate Ticket
            </h2>

            <!-- Ticket Information -->
            <div class="mb-4">
                <div
                    class="mb-2 text-white p-2 rounded-md"
                    :class="
                        !props.ticket.validated
                            ? 'bg-green-500'
                            : 'bg-amber-500'
                    "
                >
                    <strong>Name:</strong> {{ props.ticket.name }}
                </div>
                <div class="mb-2">
                    <strong>Category:</strong>
                    {{ props.ticket.ticket_category_name }}
                </div>
                <div class="mb-2">
                    <strong>Event:</strong> {{ props.ticket.event_title }}
                </div>
                <div class="mb-2">
                    <strong>Already Validated:</strong>
                    {{ props.ticket.validated ? 'Yes' : 'No' }}
                </div>
                <div class="mb-2" v-if="props.ticket.validated">
                    <strong>Validated By:</strong>
                    {{ props.ticket.validated_by_name }}
                </div>
            </div>

            <!-- Action Button -->
            <Link
                as="button"
                method="PATCH"
                :disabled="props.ticket.validated"
                :href="
                    route('ticket.validate', {
                        event: props.event_id,
                        ticket: props.ticket.uuid,
                    })
                "
                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 bg-green-400"
            >
                Validate
            </Link>
        </div>
    </div>
</template>
