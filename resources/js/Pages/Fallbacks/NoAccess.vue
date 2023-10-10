<template>
    <Card>
        <h1 class="text-lg font-semibold">Nu ai acces la această rută! 😢</h1>
        <p>Ne pare rău, dar nu puteți accesa această rută.</p>
        <Link class="mt-8 font-bold text-blue-700" as="a" href="/"
            >Inapoi la pagina principală</Link
        >
    </Card>
</template>

<script setup>
import { Card } from "@/Components/UI";
import { Link } from "@inertiajs/vue3";
</script>

<style lang="scss" scoped></style>
