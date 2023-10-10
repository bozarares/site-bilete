<script setup>
import Button from '@/Components/UI/Button/Button.vue';
import { Link } from '@inertiajs/vue3';
import {
    AdjustmentsHorizontalIcon,
    CurrencyEuroIcon,
} from '@heroicons/vue/24/outline';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import Select from '../Select/Select.vue';
import { ref } from 'vue';
import axios from 'axios';
import { useToastStore } from '@/store/toastStore';
import { useCartStore } from '@/store/cartStore';

dayjs.extend(relativeTime);
dayjs.locale('en');

const toastStore = useToastStore();
const cartStore = useCartStore();

const buyTicketNumber = ref(1);
const selectNumbers = [1, 2, 3, 4, 5];

const props = defineProps({
    ticketCategory: { type: Object },
    eventId: String,
    editPermissions: Boolean,
});

const addToCart = () => {
    axios
        .post(route('cart.add'), {
            ticket_category_id: props.ticketCategory.id,
            quantity: buyTicketNumber.value,
        })
        .then((response) => {
            if (response.data && response.data.message) {
                toastStore.addToast({
                    title: 'Cart notification',
                    message: response.data.message,
                    status: 'success',
                });
                cartStore.setCartItemCount(response.data.cart_item_count);
            }
        })
        .catch((error) => {
            toastStore.addToast({
                title: 'Cart notifiation',
                status: 'warning',
                message: error.response.data.message,
            });
        });
};
</script>

<template>
    <div
        class="relative flex flex-col border-[1px] border-gray-200 px-2 py-2 rounded-md shadow-sm gap-2"
    >
        <div
            class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none"
        >
            <div
                v-if="ticketCategory.paused"
                class="absolute transform origin-top -rotate-45 bg-red-500 text-center text-white font-semibold py-1 -left-12 top-8 w-[10em]"
            >
                Paused Selling
            </div>
            <div
                v-else-if="ticketCategory.remaining_tickets == 0"
                class="absolute transform origin-top -rotate-45 bg-amber-500 text-center text-white font-semibold py-1 -left-12 top-8 w-[10em]"
            >
                Sold Out
            </div>
        </div>

        <div
            class="grid md:grid-cols-4 grid-cols-2 grid-rows-2 md:grid-rows-1 w-full gap-4 items-center"
        >
            <div class="col-span-2 md:col-span-2 flex flex-col">
                <div class="font-bold text-lg md:text-xl">
                    {{ ticketCategory.name }}
                </div>
                <div class="font-bold text-sm text-black/60">
                    {{ ticketCategory.description }}
                </div>
            </div>
            <div class="text-center">
                <div class="font-bold text-sm">Price</div>
                <div class="font-bold text-md">${{ ticketCategory.price }}</div>
            </div>
            <div class="text-center">
                <div class="font-bold text-sm">Remaining tickets</div>
                <div class="font-bold text-md">
                    {{ ticketCategory.remaining_tickets }}
                </div>
            </div>
        </div>
        <p class="font-bold text-sm text-black/60 text-center">
            This category is valid from
            {{ dayjs(ticketCategory.start_date).format('DD/MM/YYYY HH:mm') }} to
            {{ dayjs(ticketCategory.end_date).format('DD/MM/YYYY HH:mm') }}
        </p>
        <div
            class="flex justify-center items-center gap-1 flex-col sm:flex-row"
        >
            <div class="w-full sm:w-32">
                <Select
                    v-model="buyTicketNumber"
                    label="Tickets"
                    :select="selectNumbers"
                    :disabled="
                        !!ticketCategory.paused ||
                        !!ticketCategory.remaining_tickets == 0
                    "
                    :options="{
                        dismissable: false,
                        borderStyle: 'bordered',
                    }"
                ></Select>
            </div>
            <Button
                as="button"
                @click="addToCart"
                :disabled="
                    !!ticketCategory.paused ||
                    !!ticketCategory.remaining_tickets == 0
                "
                :options="{
                    color: 'green',
                    leftIcon: CurrencyEuroIcon,
                }"
            >
                Buy {{ buyTicketNumber <= 1 ? 'Ticket' : 'Tickets' }}
            </Button>

            <div v-if="editPermissions" class="text-right">
                <Button
                    :is="Link"
                    :href="
                        route('ticket_category.actions', {
                            event: eventId,
                            ticket_category: ticketCategory.id,
                        })
                    "
                    :options="{
                        color: 'amber',
                        leftIcon: AdjustmentsHorizontalIcon,
                    }"
                    >Actions</Button
                >
            </div>
        </div>
    </div>
</template>
