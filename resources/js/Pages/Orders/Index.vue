<template>
    <Card>
        <div v-if="props.orders.length == 0">
            <p class="text-gray-600">You don't have tickets ordered</p>
        </div>
        <div
            v-else
            v-for="order in orders"
            :key="order.id"
            class="mb-5 pb-5 border-b border-gray-300"
        >
            <h2 class="text-lg font-semibold mb-3 text-gray-800">
                Order ID: {{ order.id }}
            </h2>
            <p class="text-md text-gray-600">
                Payment Reference: {{ order.checkout_id }}
            </p>

            <div v-for="item in order.items" :key="item.id" class="mb-4">
                <p class="text-md text-gray-600">
                    <strong class="font-medium text-gray-700">{{
                        item.event_name
                    }}</strong>
                    - {{ item.name }}, Price: ${{ item.price }}, Quantity:
                    {{ item.quantity }}
                </p>
            </div>

            <p class="text-md text-gray-600">Total Paid: ${{ order.price }}</p>
        </div>
    </Card>
</template>

<script setup>
import { Card } from "@/Components/UI";
const props = defineProps({ orders: Array });
</script>
