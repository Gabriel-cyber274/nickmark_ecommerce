<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(false);

const handleStart = () => {
    isLoading.value = true;
};

const handleFinish = () => {
    isLoading.value = false;
};

let cleanupStart = null;
let cleanupFinish = null;
let cleanupCancel = null;
let cleanupError = null;

onMounted(() => {
    cleanupStart = router.on('start', handleStart);
    cleanupFinish = router.on('finish', handleFinish);
    cleanupCancel = router.on('cancel', handleFinish);
    cleanupError = router.on('error', handleFinish);
});

onUnmounted(() => {
    if (cleanupStart) cleanupStart();
    if (cleanupFinish) cleanupFinish();
    if (cleanupCancel) cleanupCancel();
    if (cleanupError) cleanupError();
});
</script>

<template>
    <transition name="fade">
        <div v-if="isLoading" class="global-loader-overlay" aria-hidden="true">
            <div class="global-loader-spinner">
                <div class="spinner"></div>
                <p class="loader-text">Loading...</p>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.global-loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.6);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
}

.global-loader-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;
    border-top-color: #c96;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loader-text {
    color: #6b7280;
    font-size: 14px;
    font-weight: 500;
    margin: 0;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>