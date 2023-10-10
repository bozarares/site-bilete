<script setup>
import { Button, Input } from '@/Components/UI';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    EyeIcon,
    EyeSlashIcon,
    KeyIcon,
    UserIcon,
    AtSymbolIcon,
} from '@heroicons/vue/24/outline';

const loading = ref(false);
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    loading.value = true;

    form.post(route('register'), {
        onFinish: () => {
            loading.value = false;
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Register" />
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
                    src="/images/register.jpg"
                    alt="Login image"
                />
                <div
                    class="absolute flex items-center gap-2 justify-center select-none pointer-events-none w-1/2 text-center top-1/4 left-1/2 transform -translate-x-1/2 -translate-y-1/2 font-bold text-xl fill-white bg-white bg-opacity-90 px-2 py-1 rounded shadow-lg"
                >
                    Sign up to
                    <img
                        class="h-8"
                        src="/images/logo/logo-no-background.svg"
                        alt=""
                    />
                </div>
            </div>
            <div class="w-full px-8 flex flex-col items-center gap-8">
                <Input
                    v-model="form.name"
                    label="Name"
                    type="text"
                    name="name"
                    :error="form.errors.name"
                    :options="{ borderStyle: 'bordered', leftIcon: UserIcon }"
                ></Input>
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
                <Input
                    v-model="form.password_confirmation"
                    label="Password Confirmation"
                    type="password"
                    name="password_confirmation"
                    :error="form.errors.password"
                    :options="{
                        borderStyle: 'bordered',
                        leftIcon: KeyIcon,
                        passwordIcon: { show: EyeIcon, hide: EyeSlashIcon },
                    }"
                ></Input>
                <Button class="w-full h-6 px-4 py-6" :loading="!!loading"
                    >Sign up with email</Button
                >
                <Link :href="route('login')"
                    >You already have and accout? Sign in!</Link
                >
            </div>
        </form>
    </div>
</template>
