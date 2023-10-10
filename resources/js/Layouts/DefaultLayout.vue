<script setup>
import {
    DropdownHeader,
    DropdownItem,
    DropdownMenu,
    DropdownSeparator,
    Avatar,
    Toast,
} from '@/Components/UI';

import { ShoppingCartIcon } from '@heroicons/vue/24/outline';

import { useToastStore } from '@/store/toastStore';
import { useCartStore } from '@/store/cartStore';

import { usePage, Link } from '@inertiajs/vue3';
import { computed, watchEffect, watch } from 'vue';

const page = usePage();
const toastStore = useToastStore();
const cartStore = useCartStore();

// Initialize cart item count
watchEffect(() => {
    cartStore.initializeCartItemCount(
        page.props.auth && page.props.auth.user
            ? page.props.auth.user.cart_item_count
            : 0,
    );
});

// Initialize the toast store
const toasts = toastStore.toasts;

// Define props
const props = defineProps({
    event_id: { type: String },
    ticket_category: { type: Object, default: () => {} },
});

// Watch for toast messages
const message = computed(() => page.props.message);

watch(message, (newMessage) => {
    if (newMessage) {
        const parsedMessage = JSON.parse(newMessage);

        toastStore.addToast({
            title: parsedMessage.title || null,
            message: parsedMessage.content,
            status: parsedMessage.status || 'success',
        });
    }
});
</script>

<template>
    <!-- Toasts -->
    <Toast v-model="toasts"></Toast>

    <!-- Default layout -->
    <div class="flex flex-col min-h-screen">
        <div
            class="before:fixed before:bg-gray-100 before:top-0 before:left-0 before:right-0 before:h-4 before:-z-10 sticky top-4 z-50 px-8 flex justify-between items-center shadow m-4 rounded-md bg-white"
        >
            <Link href="/">
                <img
                    class="w-36 h-16 fill-current object-contain text-gray-500"
                    src="/images/logo/logo-no-background.png"
                />
            </Link>
            <div class="flex gap-8 items-center">
                <Link
                    class="font-medium text-gray-700 hidden md:block"
                    :href="route('events.index')"
                    >Events</Link
                >
                <Link
                    class="font-medium text-gray-700 hidden md:block"
                    :href="route('tickets.index')"
                    >My Tickets</Link
                >
                <Link :href="route('cart.index')" class="relative font-medium">
                    <ShoppingCartIcon class="w-6 h-6" />
                    <span
                        v-if="
                            $page.props.auth.user &&
                            cartStore.cart_item_count > 0
                        "
                        class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
                    >
                        {{ cartStore.cart_item_count }}
                    </span>
                </Link>

                <!-- Dropdown menu -->
                <DropdownMenu align="right">
                    <template v-slot:dropdownMenuButton>
                        <Avatar
                            :name="
                                $page.props.auth.user
                                    ? $page.props.auth.user.name
                                    : null
                            "
                            size="small"
                        ></Avatar>
                    </template>
                    <DropdownHeader align="left">
                        <div>Hello 😁</div>
                    </DropdownHeader>
                    <DropdownSeparator />
                    <template v-if="$page.props.auth.user">
                        <DropdownItem :is="Link" :href="route('dashboard')">
                            Dashboard
                        </DropdownItem>
                        <DropdownItem :is="Link" :href="route('profile.edit')">
                            Profile
                        </DropdownItem>
                        <DropdownItem :is="Link" :href="route('orders.index')">
                            Orders
                        </DropdownItem>
                        <DropdownSeparator />
                        <DropdownItem
                            method="POST"
                            as="button"
                            :is="Link"
                            :href="route('logout')"
                        >
                            Log Out
                        </DropdownItem>
                    </template>
                    <template v-else>
                        <DropdownItem :is="Link" :href="route('login')">
                            Login
                        </DropdownItem>
                        <DropdownItem :is="Link" :href="route('register')">
                            Register
                        </DropdownItem>
                    </template>
                </DropdownMenu>
            </div>
        </div>
        <!-- <div class="px-8 flex justify-between items-center border-b-2 shadow-sm"> -->
        <!-- Main Slot -->
        <main class="flex-grow flex flex-col items-center">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white rounded-md shadow m-4 sm:m-2 flex-shrink-0">
            <div
                class="w-full p-4 flex flex-col sm:flex-row sm:items-center justify-between"
            >
                <span class="text-sm text-center text-gray-500">
                    © 2023 <a href="#" class="hover:underline">Tiketino</a>.
                    All Rights Reserved.
                </span>
                <ul
                    class="flex flex-col sm:flex-row items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0"
                >
                    <li class="my-1 sm:my-0 sm:mx-3">
                        <a href="#" class="hover:underline">About</a>
                    </li>
                    <li class="my-1 sm:my-0 sm:mx-3">
                        <a href="#" class="hover:underline">Privacy Policy</a>
                    </li>
                    <li class="my-1 sm:my-0 sm:mx-3">
                        <a href="#" class="hover:underline">Licensing</a>
                    </li>
                    <li class="my-1 sm:my-0 sm:mx-3">
                        <a href="#" class="hover:underline">Contact</a>
                    </li>
                </ul>
            </div>
        </footer>
    </div>
</template>
