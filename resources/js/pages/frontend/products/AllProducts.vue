<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';
import { Head} from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const translations = computed(() => {
    return (page.props.translations as any)?.products || {};
});
</script>

<template>
<UserLayout>
    <Head :title="translations.products || 'Products'" />
    <ProductSection
      :title="translations.title || 'Products List'"
      :subtitle="translations.subtitle || 'Explore our wide range of high-quality products tailored to your needs.'"
      :showPagination="true"
      :showFilter="true"
      :categories="page.props.categories"
       :selectedCategory="page.props.selectedCategory"
    />
  </UserLayout>
</template>
