<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    ticket: { type: Object, default: () => {} },
});

const form = useForm({
    name: props.ticket.name,
});
const submit = () => {
    form.patch(
        route("tickets.update", {
            ticket: props.ticket.uuid,
        })
    );
};
</script>

<template>
    <Head title="Edit ticket" />
    <Link
        :href="route('tickets.show', { ticket: props.ticket.uuid })"
        as="button"
        >Back</Link
    >
    <form
        @submit.prevent="submit"
        class="flex flex-col w-80 p-4 items-center gap-4"
    >
        <div class="w-full">
            <label for="name">Name</label>
            <input class="w-full" type="text" id="name" v-model="form.name" />
            <p v-if="form.errors.name">{{ form.errors.name }}</p>
        </div>

        <button type="submit">Edit ticket name</button>
    </form>
</template>
