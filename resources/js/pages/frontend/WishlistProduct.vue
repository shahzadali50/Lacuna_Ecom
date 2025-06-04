<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Row, Col } from 'ant-design-vue';

const page = usePage();
const translations = computed(() => {
  return (page.props.translations as any)?.products || {};
});
const products = computed(() => page.props.products || { data: [] });
</script>

<template>
  <UserLayout>
    <Head :title="translations.wishlist || 'Wishlist'" />
    <section
      class="bg-cover bg-center py-16 sm:py-24"
      style="background-image: url('/assets/images/page-header-bg.jpg');">
      <div class="container mx-auto">
        <Row>
          <Col :span="24" class="text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-0">
              Wishlist Products
            </h1>
          </Col>
        </Row>
      </div>
    </section>
    <section>
      <div class="container mx-auto">
        <Row class="items-center m-5">
          <Col :span="12">
            <a-breadcrumb>
              <a-breadcrumb-item>Home</a-breadcrumb-item>
              <a-breadcrumb-item>Wishlist</a-breadcrumb-item>
            </a-breadcrumb>
          </Col>
        </Row>
      </div>
    </section>

    <ProductSection
      :showPagination="true"
      :showFilter="false"
      sectionClass="py-4"
    />
  </UserLayout>
</template>
