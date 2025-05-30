<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import ProductSection from '@/components/frontend/ProductSection.vue';
import { Head } from '@inertiajs/vue3';
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
        <section class="bg-cover bg-center py-16 sm:py-24"
            style="background-image: url('/assets/images/page-header-bg.jpg');">
            <div class="container mx-auto">
                <a-row>
                    <a-col :span="24" class="text-center">
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-0">Shop By Products</h1>
                    </a-col>
                </a-row>
            </div>
        </section>
        <section>
            <div class="container mx-auto">
                <a-row>
                    <a-col :span="24" class="my-2 ml-5">
                        <a-breadcrumb>
                            <a-breadcrumb-item>Home</a-breadcrumb-item>
                            <a-breadcrumb-item>Products</a-breadcrumb-item>
                        </a-breadcrumb>
                    </a-col>
                    <a-col span="24" class="my-2">
                        <hr>
                    </a-col>
                </a-row>
            </div>
        </section>

        <ProductSection :showPagination="true" :showFilter="true" :categories="page.props.categories"
            :selectedCategory="page.props.selectedCategory"  />
    </UserLayout>
</template>
