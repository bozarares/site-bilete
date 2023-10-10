<script setup>
import { Card, Button, Input, Checkbox } from '@/Components/UI';
import { computed, ref, watch } from 'vue';
import {
    ArrowRightIcon,
    DevicePhoneMobileIcon,
    EnvelopeIcon,
    MapPinIcon,
    TrashIcon,
    UserIcon,
} from '@heroicons/vue/24/outline';
import axios from 'axios';
import { useCartStore } from '@/store/cartStore';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({ cart: Object });
const auth = usePage().props.auth.user;

const proceed = ref(false);
const cartStore = useCartStore();

const form = useForm({
    full_name: auth.name,
    address: '',
    email: auth.email,
    phone: '',
    gdpr: false,
});

const submit = () => {
    form.post(route('checkout'));
};

const cartItems = ref(props.cart ? props.cart.items : []);

const cartTotal = computed(() => {
    if (props.cart && cartItems.value) {
        return cartItems.value.reduce(
            (total, item) => total + item.price * item.quantity,
            0,
        );
    }
    return 0;
});

watch(
    () => props.cart,
    (newVal) => {
        cartItems.value = newVal ? newVal.items : [];
    },
);

const currency = (value) => {
    return `$${parseFloat(value).toFixed(2)}`;
};

const editCartItem = (id, new_quanity) => {
    axios
        .put(route('cart.edit'), {
            cart_item_id: id,
            quantity: new_quanity,
        })
        .then((response) => {
            if (response.data && response.data.message) {
            }
        })
        .catch((error) => {
            console.error(
                'A apărut o eroare la adăugarea în coș:',
                error.message,
            );
            console.error('Error Response:', error.response);
        });
};

const deleteCartItem = (id) => {
    axios
        .delete(route('cart.delete'), {
            data: {
                cart_item_id: id,
            },
        })
        .then((response) => {
            if (response.data && response.data.message) {
                cartStore.setCartItemCount(response.data.cart_item_count);
                cartItems.value = cartItems.value.filter(
                    (item) => item.id !== id,
                );
            }
        })
        .catch((error) => {
            console.error(
                'A apărut o eroare la adăugarea în coș:',
                error.message,
            );
            console.error('Error Response:', error.response);
        });
};
</script>
<template>
    <Card class="bg-gray-100 min-h-screen md:w-[50em] w-full">
        <div class="mb-6">
            <p class="text-2xl font-bold mb-4">Your Cart</p>
        </div>
        <div v-if="!props.cart">
            <p class="text-gray-600">You don't have items in the cart</p>
        </div>
        <div v-else v-auto-animate="{ duration: 100, easing: 'ease-in-out' }">
            <div
                v-auto-animate="{ duration: 100, easing: 'ease-in-out' }"
                v-for="item in cartItems"
                :key="item.event_name"
                class="mb-6 border-b pb-4"
            >
                <p class="mb-2 text-gray-800">
                    <strong>Event Name: </strong>{{ item.event_name }}
                </p>
                <div class="flex justify-between mb-2">
                    <p class="text-gray-600">
                        <strong>Category: </strong>{{ item.name }}
                    </p>
                    <p class="text-gray-700">
                        {{ item.quantity }} * {{ currency(item.price) }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Button
                        :options="{ color: 'red', leftIcon: TrashIcon }"
                        @click="
                            () => {
                                deleteCartItem(item.id);
                            }
                        "
                    ></Button>
                    <Button
                        :options="{ color: 'red' }"
                        @click="
                            () => {
                                if (item.quantity > 1) {
                                    item.quantity--;
                                    editCartItem(item.id, item.quantity);
                                } else {
                                    deleteCartItem(item.id);
                                }
                            }
                        "
                        class="bg-blue-500 text-white px-3 py-1"
                        >-</Button
                    >
                    <p class="bg-white px-4 py-[0.35rem] border rounded-lg">
                        {{ item.quantity }}
                    </p>
                    <Button
                        @click="
                            () => {
                                item.quantity++;
                                editCartItem(item.id, item.quantity);
                            }
                        "
                        :options="{ color: 'green' }"
                        class="bg-blue-500 text-white px-3 py-1"
                        >+</Button
                    >
                </div>
            </div>
            <div class="mt-6">
                <p class="text-lg font-semibold text-gray-800">
                    Total: {{ currency(cartTotal) }}
                </p>
            </div>
        </div>
        <div class="mt-8 flex items-center justify-center w-full">
            <Button
                v-if="!proceed && cartItems.length != 0"
                @click="proceed = !proceed"
                :options="{ rightIcon: ArrowRightIcon }"
                >Proceed</Button
            >
            <form
                @submit.prevent="submit"
                class="w-full md:w-2/3 flex flex-col gap-8"
                v-if="proceed"
            >
                <Input
                    v-model="form.full_name"
                    :error="form.errors.full_name"
                    label="Full Name"
                    type="text"
                    name="full_name"
                    :options="{ borderStyle: 'bordered', leftIcon: UserIcon }"
                    class="w-full"
                >
                </Input>
                <Input
                    v-model="form.address"
                    :error="form.errors.address"
                    label="Invoice address"
                    type="text"
                    name="address"
                    :options="{ borderStyle: 'bordered', leftIcon: MapPinIcon }"
                    class="w-full"
                >
                </Input>
                <Input
                    v-model="form.email"
                    :error="form.errors.email"
                    label="Email"
                    type="email"
                    name="email"
                    :options="{
                        borderStyle: 'bordered',
                        leftIcon: EnvelopeIcon,
                    }"
                    class="w-full"
                >
                </Input>
                <Input
                    v-model="form.phone"
                    :error="form.errors.phone"
                    label="Phone number"
                    type="text"
                    name="phone"
                    :options="{
                        borderStyle: 'bordered',
                        leftIcon: DevicePhoneMobileIcon,
                    }"
                    class="w-full"
                >
                </Input>
                <Checkbox
                    label="I agree to the processing of my personal data"
                    color="gray"
                    v-model="form.gdpr"
                />
                <Button as="button" type="submit" :disabled="!form.gdpr"
                    >Pay with Stripe</Button
                >
            </form>
        </div>
    </Card>
</template>

<style scoped>
/* Add any specific styles if required */
</style>

<style scoped>
/* Add any specific styles if required */
</style>
