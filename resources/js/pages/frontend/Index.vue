<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import BannerSection from '@/components/frontend/BannerSection.vue';
import ShopByCategory from '@/components/frontend/CategorySection.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';


</script>

<template>
  <UserLayout>
    <Head title="Welcome" />
    <BannerSection />
    <ProductSection
    :showAllProductsButton="true"/>
    <ShopByCategory />
  </UserLayout>
</template>
