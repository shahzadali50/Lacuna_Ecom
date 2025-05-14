<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Row, Col } from 'ant-design-vue';
import CartItems from '@/components/frontend/CartItems.vue';
</script>

<template>
    <UserLayout>

        <Head title="checkout" />
        <section class="mt-16">
            <div class="container mx-auto px-2 sm:px-4">
                <Row>
                    <Col :span="10">
                    <CartItems />
                    </Col>
                </Row>
            </div>
        </section>
    </UserLayout>
</template>
