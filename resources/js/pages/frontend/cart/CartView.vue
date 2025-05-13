<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Row, Col } from 'ant-design-vue';




</script>

<template>
    <UserLayout>

        <Head title="Checkout" />
        <section class="mt-16">
            <div class="container mx-auto px-2 sm:px-4">
                <Row>
                    <Col :span="12">
                    <h1>cartview
                    </h1>

                    </Col>
                </Row>

            </div>

        </section>

    </UserLayout>
</template>
