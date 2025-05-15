<script setup lang="ts">
import UserLayout from "@/layouts/UserLayout.vue";
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { Row, Col } from "ant-design-vue";
import CartItems from "@/components/frontend/CartItems.vue";
import { Button } from "@/components/ui/button";
import { ShoppingCartOutlined } from "@ant-design/icons-vue";
import { Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";

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
const form = useForm({

    name: '',
    email: '',
    phone_number: '',
    address: '',
    country: '',
    state: '',
    city: '',
    postal_code: '',
    order_notes: '',
    payment_method: '',
})

const orderGenerate = () => {
    form.post(route('order.generate'), {
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>
    <UserLayout>

        <Head title="Checkout" />
        <section class="py-14">
            <div class="container mx-auto px-2 sm:px-4">
                <Row v-if="!cart || cart.length === 0" class="py-10">
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

                <form class="w-full" v-else @submit.prevent="orderGenerate()">
                    <Row class="justify-between px-6">
                        <Col :xs="24" :md="10" class="mb-5">
                        <div>
                            <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">1.</h1>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                Billing details
                            </h1>
                        </div>
                        <div class="mb-4">
                            <label class="block">Full Name <span class="text-red-500">*</span></label>
                            <a-input v-model:value="form.name" class="mt-2 w-full" placeholder="Enter Your Full Name" />
                            <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">Email <span class="text-red-500">*</span></label>
                            <a-input type="email" v-model:value="form.email" class="mt-2 w-full" placeholder="Enter Your Email" />
                            <div v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">Phone Number <span class="text-red-500">*</span></label>
                            <a-input type="number" v-model:value="form.phone_number" class="mt-2 w-full"
                                placeholder="Enter Your Phone Number" />
                            <div v-if="form.errors.phone_number" class="text-red-500">{{ form.errors.phone_number }}
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block">Address <span class="text-red-500">*</span></label>
                            <a-textarea v-model:value="form.address" class="mt-2 w-full" placeholder="Enter Your Address"
                                :rows="4" />
                            <div v-if="form.errors.address" class="text-red-500">{{ form.errors.address }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">Country <span class="text-red-500">*</span></label>
                            <a-input v-model:value="form.country" class="mt-2 w-full" placeholder="Enter Your Country" />
                            <div v-if="form.errors.country" class="text-red-500">{{ form.errors.country }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">State <span class="text-red-500">*</span></label>
                            <a-input v-model:value="form.state" class="mt-2 w-full" placeholder="Enter Your State" />
                            <div v-if="form.errors.state" class="text-red-500">{{ form.errors.state }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">City <span class="text-red-500">*</span></label>
                            <a-input v-model:value="form.city" class="mt-2 w-full" placeholder="Enter Your City" />
                            <div v-if="form.errors.city" class="text-red-500">{{ form.errors.city }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">Postal Code <span class="text-red-500">*</span></label>
                            <a-input type="number" v-model:value="form.postal_code" class="mt-2 w-full"
                                placeholder="Enter Your Postal Code" />
                            <div v-if="form.errors.postal_code" class="text-red-500">{{ form.errors.postal_code }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block">Order notes (optional)</label>
                            <a-textarea v-model:value="form.order_notes" class="mt-2 w-full"
                                placeholder="Enter Your Order Notes" :rows="4" />
                            <div v-if="form.errors.order_notes" class="text-red-500">{{ form.errors.order_notes }}</div>
                        </div>
                        </Col>
                        <Col :xs="24" :md="10" class="mb-5">
                        <div>
                            <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">2.</h1>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Check Your Order</h1>
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
                            <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">3.</h1>
                            <div v-if="form.errors.payment_method" class="text-red-500">{{ form.errors.payment_method }}</div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Payment Method</h1>
                            <div class="">
                                <div class="flex items-center">
                                    <a-radio-group v-model:value="form.payment_method" class="ml-2">
                                        <a-radio value="Cash On Delivery">Cash On Delivery</a-radio>
                                        </a-radio-group>
                                </div>
                                <p class="text-gray-500 text-sm ml-6">
                                    Pay with cash upon delivery.
                                </p>
                            </div>
                            <div>
                                <a-button html-type="submit" class="w-full btn-primary mt-5"> Place Order </a-button>
                            </div>
                        </div>
                        </Col>
                    </Row>
                </form>
            </div>
        </section>
    </UserLayout>
</template>
