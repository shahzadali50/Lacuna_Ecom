<script setup lang="ts">
import { useFlashMessages } from '@/composables/useFlashMessages';
import Header from '@/components/frontend/Header.vue';
import Footer from '@/components/frontend/Footer.vue';
useFlashMessages();
</script>
<template>
   <Header />
        <main>
            <slot />
        </main>
        <Footer />
</template>

<style scoped></style>
