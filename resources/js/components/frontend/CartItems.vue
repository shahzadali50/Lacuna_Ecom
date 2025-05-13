<script setup lang="ts">
import { computed } from 'vue';
import { DeleteOutlined, ShoppingCartOutlined } from '@ant-design/icons-vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const translations = computed(() => page.props.translations || {});

const cart = computed(() => {
    const data = page.props.cart as any[] || [];
    return data.map(item => ({
        ...item,
        quantity: item.qty || 0,
    }));
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
    router.post(route('cart.remove'), { id: productId }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Cart will be automatically updated through page props
        },
        onError: (errors) => {
            console.error('Remove failed', errors);
        }
    });
};
</script>

<template>
    <div class="flex-1 overflow-y-auto">
        <div v-if="!cart || cart.length === 0" class="text-center py-8">
            <ShoppingCartOutlined class="text-4xl text-gray-400 mb-4" />
            <p class="text-gray-500">{{ translations.cart_empty || 'Your cart is empty' }}</p>
        </div>
        <div v-else class="space-y-4">
            <div v-for="item in cart" :key="item.id" class="flex items-start gap-4 border-b">
                <img :src="'/storage/' + item.thumnail_img" :alt="item.name"
                    class="w-20 h-20 object-cover rounded">
                <div class="flex-1">
                    <div>
                        <h3 class="font-medium">{{ item.name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <a-button size="small" @click="updateQuantity(item.id, 'decrease')">-</a-button>
                        <span class="text-gray-600">{{ item.quantity }}</span>
                        <a-button size="small" @click="updateQuantity(item.id, 'add')">+</a-button>
                        <a-button type="text" shape="circle" class="text-red-500" @click="removeItem(item.id)">
                            <template #icon>
                                <DeleteOutlined />
                            </template>
                        </a-button>
                    </div>
                    <div class="flex justify-between items-start mt-2">
                        <p>{{ item.quantity }} X {{ formatPrice(item.final_price) }}</p>
                    </div>
                    <p class="text-primary font-medium mt-2">
                        {{ translations.total || 'Total' }}: {{ formatPrice(item.total_price) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
