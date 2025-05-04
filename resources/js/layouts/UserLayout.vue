<script setup lang="ts">
import { useFlashMessages } from '@/composables/useFlashMessages';
import Header from '@/components/frontend/Header.vue';
import Footer from '@/components/frontend/Footer.vue';
useFlashMessages();
</script>
<template>
    <div class="flex flex-col min-h-screen">
        <Header />
        <main class="flex-grow ">
            <slot />
        </main>
        <Footer />
    </div>
</template>

<style scoped></style>
