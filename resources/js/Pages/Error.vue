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
  <div class="min-h-screen flex flex-col items-center justify-center page-fade">
    <h1 class="text-6xl font-bold animate-bounce-slow">{{ status }}</h1>
    <p class="mt-2 text-xl">{{ titles[status] ?? 'Something went wrong' }}</p>
    <Link href="/" class="mt-6 underline btn-lift">Go home</Link>
  </div>
</template>

<style scoped>
.main {
    padding: 20px;
}

/* ===== Smooth Page Animations ===== */
.page-fade {
    animation: pageFadeIn 0.6s ease-out forwards;
}

@keyframes pageFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes animate-bounce-slow {
    0%, 100% {
        transform: translateY(-25%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}
.animate-bounce-slow {
    animation: animate-bounce-slow 1.2s infinite;
}

/* Staggered list items */
.stagger > * {
    opacity: 0;
    animation: fadeUp 0.5s ease forwards;
}
.stagger > *:nth-child(1) { animation-delay: 0.05s; }
.stagger > *:nth-child(2) { animation-delay: 0.1s; }
.stagger > *:nth-child(3) { animation-delay: 0.15s; }
.stagger > *:nth-child(4) { animation-delay: 0.2s; }
.stagger > *:nth-child(5) { animation-delay: 0.25s; }
.stagger > *:nth-child(6) { animation-delay: 0.3s; }

@keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
}

/* Hover lift card */
.lift-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.lift-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 35px rgba(0,0,0,0.12);
}

/* Image hover zoom */
.zoom-img {
    overflow: hidden;
}
.zoom-img img {
    transition: transform 0.5s ease;
}
.zoom-img:hover img {
    transform: scale(1.06);
}

/* Button subtle lift */
.btn-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.15);
}

/* Table row hover */
.table-hover-row tr {
    transition: background 0.2s ease, transform 0.2s ease;
}
.table-hover-row tr:hover {
    background: #f8f9fa;
    transform: translateX(4px);
}

/* Focus ring for form inputs */
.focus-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    border-color: #3b82f6;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>