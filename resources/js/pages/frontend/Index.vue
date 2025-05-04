<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import BannerSection from '@/components/frontend/BannerSection.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';
import { usePage } from '@inertiajs/vue3';

interface PageProps extends Record<string, any> {
  translations: {
    banner: {
      title: string;
      subtitle: string;
      cta_text: string;
    };
    products: {
      title: string;
      subtitle: string;
      view_all: string;
      price: string;
      rating: string;
      seller: string;
      category: string;
    };
  };
}

const page = usePage<PageProps>();
</script>

<template>
  <UserLayout>
    <Head title="Welcome" />
    <BannerSection
      :title="page.props.translations.banner.title"
      :subtitle="page.props.translations.banner.subtitle"
      :cta-text="page.props.translations.banner.cta_text"
      cta-link="/marketplace"
      logo-path="assets/images/banner.jpg"
    />
    <ProductSection />
  </UserLayout>
</template>
