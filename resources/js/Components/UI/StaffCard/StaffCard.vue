<script setup>
import Button from '@/Components/UI/Button/Button.vue';
import { Link } from '@inertiajs/vue3';
import {
    AdjustmentsHorizontalIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import axios from 'axios';
import { useToastStore } from '@/store/toastStore';
const toastStore = useToastStore();

const props = defineProps({
    staff: { type: Object },
    event_id: { type: String },
    editPermissions: Boolean,
    handleStaffDelete: { type: Function, default: () => {} },
});

const deleteStaff = (staff_id) => {
    axios
        .delete(
            route('staff.destroy', {
                event: props.event_id,
                staff: staff_id,
            }),
        )
        .then((response) => {
            if (response.data && response.data.message) {
                toastStore.addToast({
                    title: 'Staff Notification',
                    message: response.data.message,
                    status: 'success',
                });
                props.handleStaffDelete(staff_id);
            }
        })
        .catch((error) => {
            toastStore.addToast({
                title: 'Staff Notification',
                status: 'warning',
                message: error.response.data.message,
            });
        });
};
</script>

<template>
    <div
        class="relative overflow-hidden flex flex-col border-[1px] border-gray-200 px-2 py-2 rounded-md shadow-sm gap-2"
    >
        <div class="grid sm:grid-cols-2 grid-cols-1 w-full gap-4 items-center">
            <div class="col-span-1 flex flex-col">
                <div class="font-bold text-sm md:text-l text-center">
                    {{ staff.user.name }}
                </div>
            </div>
            <div class="col-span-1 flex gap-1 justify-center sm:items-start">
                <div
                    v-if="staff.can_validate"
                    class="text-center px-1 py-1 bg-green-600 text-white text-xs md:text-sm font-bold rounded-full w-24"
                >
                    Can Validate
                </div>
                <div
                    v-if="staff.can_edit"
                    class="text-center px-1 py-1 bg-blue-600 text-white text-xs md:text-sm font-bold rounded-full w-24"
                >
                    Can Edit
                </div>
            </div>

            <div
                class="col-span-1 sm:col-span-2 flex sm:flex-row justify-center sm:justify-center items-center gap-4"
            >
                <Button
                    :is="Link"
                    as="button"
                    class="w-32"
                    :href="
                        route('staff.edit', {
                            event: event_id,
                            staff: staff.id,
                        })
                    "
                    :options="{
                        color: 'green',
                        leftIcon: PencilSquareIcon,
                    }"
                >
                    Edit
                </Button>
                <Button
                    as="button"
                    class="w-32"
                    @click="
                        () => {
                            deleteStaff(staff.id);
                        }
                    "
                    :options="{
                        color: 'red',
                        leftIcon: TrashIcon,
                    }"
                >
                    Remove
                </Button>
            </div>
        </div>
    </div>
</template>
