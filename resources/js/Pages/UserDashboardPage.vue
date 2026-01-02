<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, nextTick, onUnmounted } from 'vue';
import Skeleton from './Skeleton.vue'

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    auth: {
        type: Object  
    }
});

</script>

<template>
    <Skeleton page="dashboard" :auth="auth">
        <h1>hello {{auth.name}}</h1>
    </Skeleton>
</template>