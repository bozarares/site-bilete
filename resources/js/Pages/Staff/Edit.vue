<script setup>
import { Button, Card, Checkbox, Input } from '@/Components/UI';
import {
    ArrowLeftIcon,
    PencilSquareIcon,
    UserIcon,
} from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    event_id: { type: String },
    staff: { type: Object },
    is_organiser: { type: Boolean, default: false },
});

const form = useForm({
    event_id: props.event_id,
    user_uuid: props.staff.user_uuid,
    can_validate: !!props.staff.can_validate,
    can_edit: !!props.staff.can_edit,
});
const submit = () => {
    form.patch(
        route('staff.update', {
            event: props.event_id,
            staff: props.staff.id,
        }),
    );
};
</script>

<template>
    <Head title="Edit staff" />
    <Card>
        <form
            @submit.prevent="submit"
            class="flex flex-col w-full p-4 items-center gap-8"
        >
            <Input
                v-model="form.user_uuid"
                label="User UUID"
                type="text"
                name="user_uuid"
                :error="form.errors.user_uuid"
                :options="{ borderStyle: 'bordered', leftIcon: UserIcon }"
            >
            </Input>
            <div>
                <Checkbox
                    label="User can validate"
                    description="User can validate event tickets"
                    color="gray"
                    v-model="form.can_validate"
                />
                <Checkbox
                    label="User can edit"
                    description="User can edit event event"
                    color="gray"
                    v-model="form.can_edit"
                />
            </div>
            <div class="flex items-center gap-4">
                <Button
                    :is="Link"
                    :href="route('events.show', { event: props.event_id })"
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
                    >Edit Staff</Button
                >
            </div>
        </form>
    </Card>
</template>
