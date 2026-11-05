<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({ status: Number })

const titles = {
  403: 'Forbidden',
  404: 'Page Not Found',
  500: 'Server Error',
  503: 'Service Unavailable',
}
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center">
    <h1 class="text-6xl font-bold">{{ status }}</h1>
    <p class="mt-2 text-xl">{{ titles[status] ?? 'Something went wrong' }}</p>
    <Link href="/" class="mt-6 underline">Go home</Link>
  </div>
</template>

<style scoped>
.main {
    padding: 20px;
}
</style>