<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import CartItems from './CartItems.vue';

const page = usePage();
const translations = computed(() => {
    return (page.props.translations as any)?.products || {};
});

defineProps<{
    visible: boolean;
}>();

const emit = defineEmits<{
    'update:visible': [value: boolean]
}>();

const cart = computed(() => {
    const data = usePage().props.cart as any[] || [];
    return data;
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

const goToCheckout = () => {
    router.visit(route("cart.checkout"));
  
};

const closeDrawer = () => {
    emit('update:visible', false);
};

const drawerWidth = ref(500);

const updateDrawerWidth = () => {
    drawerWidth.value = window.innerWidth <= 576 ? 300 : 500;
};

onMounted(() => {
    updateDrawerWidth();
    window.addEventListener('resize', updateDrawerWidth);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateDrawerWidth);
});
</script>

<template>
    <a-drawer :title="translations.shopping_cart || 'Shopping Cart'" placement="right" :open="visible"
        @close="closeDrawer" :width="drawerWidth">
        <div class="flex flex-col h-full">
            <CartItems />
            <div v-if="cart && cart.length > 0" class="border-t p-4">
                <div class="flex justify-between mb-4">
                    <span class="font-medium">{{ translations.total || 'Total' }}:</span>
                    <span class="font-bold text-primary">{{ formatPrice(total) }}</span>
                </div>
                <a-button class="btn-primary" block @click="goToCheckout">
                    {{ translations.checkout || 'Proceed to Checkout' }}
                </a-button>
            </div>
        </div>
    </a-drawer>
</template>

<style scoped>
.ant-drawer-body {
    padding: 0;
}
</style>
