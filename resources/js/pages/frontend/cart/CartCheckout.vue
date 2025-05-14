<script setup lang="ts">
import UserLayout from "@/layouts/UserLayout.vue";
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { Row, Col } from "ant-design-vue";
import CartItems from "@/components/frontend/CartItems.vue";
import { Button } from "@/components/ui/button";
import { ShoppingCartOutlined } from "@ant-design/icons-vue";
import { Link } from "@inertiajs/vue3";

const page = usePage();
const translations = computed(() => {
    return (page.props.translations as any)?.products || {};
});
const cart = computed(() => {
    const data = (usePage().props.cart as any[]) || [];
    return data;
});
const total = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.total_price, 0);
});
const formatPrice = (price: number) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PKR",
        minimumFractionDigits: 2,
    }).format(price);
};
</script>

<template>
    <UserLayout>

        <Head title="Checkout" />
        <section class="py-14">
            <div class="container mx-auto px-2 sm:px-4">
            <Row  v-if="!cart || cart.length === 0" class="py-10">
            <Col span="24">

               <div class="text-center">
                <ShoppingCartOutlined class="text-7xl text-gray-400 mb-4" />
                <h4 class="text-gray-500 text-2xl">{{ translations.cart_empty || 'Your cart is empty' }}</h4>
                <Link :href="route('home')" class="inline-block mt-4">
                    <Button>Continue Shopping</Button>
                </Link>
               </div>
            </Col>
            </Row>
                <Row  class="justify-between px-6" v-else>
                    <Col :xs="24" :md="10" class="mb-5">

                        <div>
                            <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">
                                1.
                            </h1>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                Billing details
                            </h1>
                        </div>
                        <div class="mb-4">
                            <label class="block">Full Name <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your Full Name" />
                        </div>
                        <div class="mb-4">
                            <label class="block">Email <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your Email" />
                        </div>
                        <div class="mb-4">
                            <label class="block">Phone Number <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your Phone Number" />
                        </div>
                        <div class="mb-4">
                            <label class="block">Address <span class="text-red-500">*</span></label>
                            <a-textarea class="mt-2 w-full" placeholder="Enter Your Address" :rows="4" />
                        </div>
                        <div class="mb-4">
                            <label class="block">Country <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your Country" />
                        </div>
                        <div class="mb-4">
                            <label class="block">State <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your State" />
                        </div>
                        <div class="mb-4">
                            <label class="block">City <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your City" />
                        </div>
                        <div class="mb-4">
                            <label class="block">Postal Code <span class="text-red-500">*</span></label>
                            <a-input class="mt-2 w-full" placeholder="Enter Your Postal Code" />
                        </div>

                        <div class="mb-4">
                            <label class="block">Order notes (optional)</label>
                            <a-textarea class="mt-2 w-full" placeholder="Enter Your Order Notes" :rows="4" />
                        </div>

                    </Col>
                    <Col :xs="24" :md="10" class="mb-5">
                    <div>
                        <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">
                            2.
                        </h1>

                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">
                            Check Your Order
                        </h1>
                    </div>

                    <CartItems />
                    <div class="flex justify-between mb-4 border-b py-4">
                        <span class="font-medium">Subtotal</span>
                        <span class="font-bold text-primary">{{ formatPrice(total) }}</span>
                    </div>
                    <div class="flex justify-between mb-4 border-b py-4">
                        <span class="font-medium">Shipping </span>
                        <span class="font-bold text-primary"> Free Delivery</span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="font-medium">{{ translations.total || "Total" }}:</span>
                        <span class="font-bold text-primary"> {{ formatPrice(total) }} </span>
                    </div>
                    </Col>
                    <Col :xs="24" :md="10" class="mb-5">
                    <div>
                        <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">
                            3.
                        </h1>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">
                            Payment Method
                        </h1>
                        <div class="">
                            <div class="flex items-center">
                                <a-radio value="cod">
                                    Cash On Delivery
                                </a-radio>
                            </div>
                            <p class="text-gray-500 text-sm ml-6">
                                Pay with cash upon delivery.
                            </p>
                        </div>
                        <div>
                            <Button type="submit" class="w-full btn-primary mt-5"> Place Order </Button>
                        </div>

                    </div>
                    </Col>
                </Row>
            </div>
        </section>
    </UserLayout>
</template>
