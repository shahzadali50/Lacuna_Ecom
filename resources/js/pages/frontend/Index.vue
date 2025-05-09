<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import BannerSection from '@/components/frontend/BannerSection.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';
import ShopBycategory from '@/components/frontend/ShopBycategory.vue';
</script>

<template>
  <UserLayout>
    <Head title="Welcome" />
    <BannerSection />
    <ProductSection />
    <ShopBycategory />
  </UserLayout>
</template>
