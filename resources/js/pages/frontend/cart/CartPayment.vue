<script setup lang="ts">
import UserLayout from "@/layouts/UserLayout.vue";
import { computed, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { Row, Col } from "ant-design-vue";
import { Button } from "@/components/ui/button";
import { ShoppingCartOutlined, ShoppingOutlined, ArrowLeftOutlined } from "@ant-design/icons-vue";
import { Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { LoaderCircle } from "lucide-vue-next";
import type { PageProps as InertiaPageProps } from "@inertiajs/core";
import { loadStripe } from "@stripe/stripe-js";

// Add interface for page props
interface PageProps extends InertiaPageProps {
    flash?: {
        success?: string;
        error?: string;
    };
    stripe_key?: string;
}

const page = usePage<PageProps>();
const translations = computed(() => {
    return (page.props.translations as any)?.cart_checkout || {};
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

// Initialize Stripe
const stripe = ref<any>(null);
const cardElement = ref<any>(null);
const stripeError = ref<string | null>(null);

const setupStripe = async () => {
    if (page.props.stripe_key) {
        stripe.value = await loadStripe(page.props.stripe_key);
        const elements = stripe.value.elements();
        cardElement.value = elements.create("card", {
            style: {
                base: {
                    fontSize: "16px",
                    color: "#32325d",
                    "::placeholder": {
                        color: "#aab7c4",
                    },
                },
                invalid: {
                    color: "#fa755a",
                },
            },
        });
        cardElement.value.mount("#card-element");
    }
};

// Call setupStripe when component is mounted
import { onMounted } from "vue";
onMounted(() => {
    setupStripe();
});

const form = useForm({
    payment_method: "",
});

const orderGenerate = async () => {
    if (form.payment_method === "Pay with Card") {
        try {
            // Create payment intent
            const response = await fetch(route("payment.intent"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    payment_method: form.payment_method,
                }),
            });

            const { client_secret } = await response.json();

            if (!client_secret) {
                stripeError.value = "Failed to create payment intent.";
                return;
            }

            // Confirm card payment
            const result = await stripe.value.confirmCardPayment(client_secret, {
                payment_method: {
                    card: cardElement.value,
                },
            });

            if (result.error) {
                stripeError.value = result.error.message;
                return;
            }

            if (result.paymentIntent.status === "succeeded") {
                // Post to order.generate to complete the order
                form.post(route("order.generate"), {
                    onSuccess: () => {
                        form.reset();
                    },
                });
            }
        } catch (error) {
            stripeError.value = "An error occurred while processing your payment. Please try again.";
        }
    } else {
        form.post(route("order.generate"), {
            onSuccess: () => {
                form.reset();
            },
        });
    }
};
</script>
<template>
    <UserLayout>
        <Head title="Payment" />
        <section class="py-14">
            <div class="container mx-auto px-2 sm:px-4">
                <Link :href="route('cart.checkout')" class="flex items-center mb-2">
                    <ArrowLeftOutlined /> Back
                </Link>
                <a-alert class="my-1" v-if="page.props.flash?.success" :message="page.props.flash.success"
                    type="success" show-icon />
                <a-alert class="my-1" v-if="stripeError" :message="stripeError" type="error" show-icon />
                <Row v-if="!cart || cart.length === 0" class="py-10">
                    <Col span="24">
                        <div class="text-center">
                            <ShoppingCartOutlined class="text-7xl text-gray-400 mb-4" />
                            <h4 class="text-gray-500 text-2xl">
                                {{ translations.cart_empty || "Your cart is empty" }}
                            </h4>
                            <Link :href="route('home')" class="inline-block mt-4">
                                <Button>{{ translations.continue_shopping || "Continue Shopping" }}</Button>
                            </Link>
                        </div>
                    </Col>
                </Row>
                <form class="w-full" v-else @submit.prevent="orderGenerate()">
                    <div v-if="Object.keys(form.errors).length > 0"
                        class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="list-disc list-inside text-red-600">
                            <li v-for="error in Object.values(form.errors)" :key="error" class="mb-1">
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                    <Row class="justify-between px-6">
                        <Col :xs="24" :md="10" class="mb-5 order-2 md:order-1">
                            <div>
                                <h1 class="text-5xl font-bold text-green-800 mb-6 flex items-center gap-2">
                                    2.
                                </h1>
                                <div v-if="form.errors.payment_method" class="text-red-500">
                                    {{ form.errors.payment_method }}
                                </div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">
                                    {{ translations.payment_method || "Payment Method" }}
                                </h1>
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <a-radio-group v-model:value="form.payment_method" class="ml-2">
                                            <a-radio value="Cash On Delivery">
                                                {{ translations.cash_on_delivery || "Cash On Delivery" }}
                                            </a-radio>
                                        </a-radio-group>
                                    </div>
                                    <p class="text-gray-500 text-sm ml-6">
                                        {{ translations.pay_cash_on_delivery || "Pay with cash upon delivery." }}
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <a-radio-group v-model:value="form.payment_method" class="ml-2">
                                        <a-radio value="Pay with Card">Pay with Card</a-radio>
                                    </a-radio-group>
                                </div>
                                <div v-if="form.payment_method === 'Pay with Card'" class="mb-4">
                                    <label class="block">{{ translations.card_details || "Card Details" }}</label>
                                    <div id="card-element" class="mt-2 p-2 border rounded"></div>
                                </div>
                                <div>
                                    <a-button html-type="submit"
                                        class="w-full btn-primary mt-5 flex items-center justify-center py-4"
                                        :disabled="form.processing">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                                        {{ form.payment_method === 'Pay with Card' ? 'Pay Now' : 'Place Order' }}
                                        <template #icon v-if="!form.processing">
                                            <ShoppingOutlined />
                                        </template>
                                    </a-button>
                                </div>
                            </div>
                        </Col>
                        <Col :xs="24" :md="10" class="mb-5 order-1 md:order-2 p-4"
                            style="box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 mb-6">
                                    Order Summary
                                </h2>
                            </div>
                            <div class="flex justify-between mb-4 border-b py-4">
                                <span class="">{{ translations.subtotal || "Subtotal" }}</span>
                                <span class="text-slate-500">{{ formatPrice(total) }}</span>
                            </div>
                            <div class="flex justify-between mb-4 border-b py-4">
                                <span class="">{{ translations.shipping_charge || "Shipping" }}</span>
                                <span class="text-slate-500">{{ translations.free_delivery || "Free Delivery" }}</span>
                            </div>
                            <div class="flex justify-between mb-4">
                                <span class="font-bold">{{ translations.total || "Total" }}:</span>
                                <span class="text-slate-500">{{ formatPrice(total) }}</span>
                            </div>
                        </Col>
                    </Row>
                </form>
            </div>
        </section>
    </UserLayout>
</template>
