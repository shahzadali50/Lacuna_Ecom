<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { Row, Col } from 'ant-design-vue';
import { DeleteOutlined } from '@ant-design/icons-vue';
import { computed, ref } from 'vue';

const page = usePage();
const isRemoving = ref(false);
const processingItems = ref(new Set<number>());

const cart = computed(() => {
    const data = page.props.cart as any[] || [];
    return data.map(item => ({
        ...item,
        quantity: item.qty || 0,
        isRemoving: processingItems.value.has(item.id)
    }));
});

const total = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.total_price, 0);
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PKR',
        minimumFractionDigits: 2,
    }).format(price);
};

const updateQuantity = (productId: number, action: string) => {
    router.post(route('cart.add'), { id: productId, qty: 1, action }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Cart will be automatically updated through page props
        },
        onError: (errors) => {
            console.error('Cart update failed', errors);
        }
    });
};

const removeItem = (productId: number) => {
    // Prevent multiple clicks on the same item
    if (processingItems.value.has(productId)) return;

    processingItems.value.add(productId);
    isRemoving.value = true;

    router.post(route('cart.remove'), { id: productId }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Cart will be automatically updated through page props
        },
        onError: (errors) => {
            console.error('Remove failed', errors);
        },
        onFinish: () => {
            isRemoving.value = false;
            processingItems.value.delete(productId);
        }
    });
};

const proceedToCheckout = () => {
    router.visit(route('checkout'));
};
</script>

<template>
    <UserLayout>
        <Head title="checkout" />
        <section class="mt-16">
            <div class="container mx-auto px-2 sm:px-4">
                <Row>
                    <Col :span="10">
                        <div class=" p-6 rounded-lg">
                            <h1 class="text-2xl font-bold mb-6"></h1>
                            <div v-if="!cart || cart.length === 0" class="text-center py-8">
                                <p class="text-gray-500">Your cart is empty</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="item in cart" :key="item.id" class="flex items-start gap-4 border-b pb-4">
                                    <img :src="'/storage/' + item.thumnail_img" :alt="item.name"
                                        class="w-24 h-24 object-cover rounded">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-medium text-lg">{{ item.name }}</h3>

                                        </div>
                                        <div class="flex items-center gap-2 mt-2">
                                            <a-button
                                                size="small"
                                                @click="updateQuantity(item.id, 'decrease')"
                                                :disabled="item.quantity <= 1"
                                            >-</a-button>
                                            <span class="text-gray-600">{{ item.quantity }}</span>
                                            <a-button
                                                size="small"
                                                @click="updateQuantity(item.id, 'add')"
                                            >+</a-button>
                                            <a-button
                                                type="text"
                                                shape="circle"
                                                class="text-red-500"
                                                @click="removeItem(item.id)"
                                                :loading="item.isRemoving"
                                                :disabled="item.isRemoving"
                                            >
                                                <template #icon>
                                                    <DeleteOutlined />
                                                </template>
                                            </a-button>
                                        </div>
                                        <div class="flex justify-between items-start mt-2">
                                            <p>{{ item.quantity }} X {{ formatPrice(item.final_price) }}</p>
                                        </div>
                                        <p class="text-primary font-medium mt-2">
                                            Total: {{ formatPrice(item.total_price) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>
            </div>
        </section>
    </UserLayout>
</template>
