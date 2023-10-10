<template>
    <div
        class="mx-auto flex w-1/2 flex-col rounded-md border-2 border-gray-600/50 px-16 py-4 text-center"
    >
        <h1 class="text-lg font-semibold">Pagina nu a fost găsită 😢</h1>
        <p>Ne pare rău, dar pagina pe care o căutați nu există.</p>
        <Link class="mt-8 font-bold text-blue-700" as="a" href="/"
            >Inapoi la pagina principală</Link
        >
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<style lang="scss" scoped></style>
