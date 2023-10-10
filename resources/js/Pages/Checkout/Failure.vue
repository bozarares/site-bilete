<template>
    <Card>
        <h1 class="text-lg font-semibold">Plata a eșuat! 😢</h1>
        <p>
            Ne pare rău, dar tranzacția ta nu a putut fi procesată în acest
            moment.
        </p>
        <p>
            Te rugăm să verifici detaliile cardului tău și să încerci din nou.
        </p>
        <Link class="mt-8 font-bold text-blue-700" as="a" href="/"
            >Înapoi la pagina principală</Link
        >
    </Card>
</template>

<script setup>
import { Card } from "@/Components/UI";
import { Link } from "@inertiajs/vue3";
</script>
