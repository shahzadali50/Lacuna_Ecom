<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import BannerSection from '@/components/frontend/BannerSection.vue';
import ShopByCategory from '@/components/frontend/CategorySection.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';


</script>

<template>
  <UserLayout>
    <Head title="Home" />
    <BannerSection />
    <ProductSection
    :showAllProductsButton="true"
    :showTitle="true"
    :showSubTitle="true"
    sectionClass="py-14" />
    <ShopByCategory />
  </UserLayout>
</template>
<style scoped></style>
