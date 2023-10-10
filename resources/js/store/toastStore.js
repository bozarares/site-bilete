import { defineStore } from "pinia";

// Helper function to generate a unique ID
const generateUniqueId = () => {
    return Math.random().toString(36).substr(2, 9); // This will generate a random string ID
};

export const useToastStore = defineStore("toast", {
    state: () => ({
        toasts: [],
        timers: {},
    }),

    actions: {
        addToast(message) {
            const toast = {
                id: generateUniqueId(),
                status: this.status || "alert",
                title: this.title || "",
                ...message,
            };
            this.toasts.push(toast);
            const tempTimer = setTimeout(() => {
                this.removeToast(toast.id);
            }, 3000);
            this.timers[toast.id] = tempTimer;
        },

        removeToast(id) {
            const index = this.toasts.findIndex((t) => t.id === id);
            if (index !== -1) {
                this.toasts.splice(index, 1);
            }
            clearTimeout(this.timers[id]);
            delete this.timers[id];
        },
    },
});
