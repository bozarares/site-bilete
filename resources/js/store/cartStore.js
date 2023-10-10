import { defineStore } from "pinia";

export const useCartStore = defineStore("cart", {
    state: () => ({
        cart_item_count: 0,
    }),
    actions: {
        initializeCartItemCount(count) {
            this.cart_item_count = count;
        },
        setCartItemCount(count) {
            this.cart_item_count = count;
        },
    },
});
