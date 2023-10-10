<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Input, Checkbox, Button } from '@/Components/UI';
import googleIcon from '@/public/images/btn_google_signin_light_normal_web@2x.png';

import {
    EyeIcon,
    EyeSlashIcon,
    KeyIcon,
    AtSymbolIcon,
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
const loading = ref(false);
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    loading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            loading.value = false;
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
    </div>
    <div
        class="min-h-screen flex flex-col items-center pt-2 sm:pt-8 bg-gray-100"
    >
        <form
            @submit.prevent="submit"
            class="relative shadow-sm bg-white flex flex-col w-[30rem] items-center gap-8 pb-4 border-2 rounded-md overflow-hidden"
        >
            <div class="w-full relative">
                <img
                    class="w-full h-40 object-cover select-none pointer-events-none"
                    src="/images/login.jpg"
                    alt="Login image"
                />
                <div
                    class="absolute flex items-center gap-2 justify-center select-none pointer-events-none w-1/2 text-center top-1/4 left-1/2 transform -translate-x-1/2 -translate-y-1/2 font-bold text-xl fill-white bg-white bg-opacity-90 px-2 py-1 rounded shadow-lg"
                >
                    Sign in to
                    <img
                        class="h-8"
                        src="/images/logo/logo-no-background.svg"
                        alt=""
                    />
                </div>
            </div>

            <div class="w-full px-8 flex flex-col items-center gap-6">
                <div class="w-full flex flex-col items-center gap-8">
                    <Input
                        v-model="form.email"
                        label="Email"
                        type="email"
                        name="email"
                        :error="form.errors.email"
                        :options="{
                            borderStyle: 'bordered',
                            leftIcon: AtSymbolIcon,
                        }"
                    ></Input>
                    <Input
                        v-model="form.password"
                        label="Password"
                        type="password"
                        name="password"
                        :error="form.errors.password"
                        :options="{
                            borderStyle: 'bordered',
                            leftIcon: KeyIcon,
                            passwordIcon: { show: EyeIcon, hide: EyeSlashIcon },
                        }"
                    ></Input>
                </div>
                <Checkbox
                    label="Remember me"
                    color="gray"
                    v-model="form.remember"
                />
                <Button class="w-full h-6 px-4 py-6" :loading="!!loading"
                    >Sign in with email</Button
                >
                <!-- Button Google Sign In -->
                <Button
                    href="/auth/google"
                    :options="{ color: 'text' }"
                    :is="Link"
                    class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded transition-all hover:shadow-md focus:outline-none focus:shadow-outline"
                >
                    <div class="flex">
                        <img
                            src="/images/google.svg"
                            alt="Google Icon"
                            class="w-6 h-6 mr-3"
                        />
                        Continue with Google
                    </div>
                </Button>
                <Link :href="route('password.request')"
                    >Forgot your password?</Link
                >
                <Link :href="route('register')"
                    >You don't have an account? Sign up!</Link
                >
            </div>
        </form>
    </div>
</template>

<style scoped></style>
