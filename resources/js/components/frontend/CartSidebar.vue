<script setup lang="ts">
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { ShoppingCartOutlined, DeleteOutlined } from '@ant-design/icons-vue';

interface Props {
  visible: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:visible']);

const cart = computed(() => {
  const data = usePage().props.cart as any[] || [];
  return data.map(item => ({
    ...item,
    quantity: item.qty, // frontend alias
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

const goToCheckout = () => {
  router.visit(route('cart.view'));
};

const closeDrawer = () => {
  emit('update:visible', false);
};
</script>

<template>
  <a-drawer
    title="Shopping Cart"
    placement="right"
    :visible="visible"
    @close="closeDrawer"
    width="400"
  >
    <div class="flex flex-col h-full">
      <div class="flex-1 overflow-y-auto">
        <div v-if="!cart || cart.length === 0" class="text-center py-8">
          <ShoppingCartOutlined class="text-4xl text-gray-400 mb-4" />
          <p class="text-gray-500">Your cart is empty</p>
        </div>
        <div v-else class="space-y-4">
          <div v-for="item in cart" :key="item.id" class="flex items-center gap-4 p-4 border-b">
            <img :src="'/storage/' + item.thumnail_img" :alt="item.name" class="w-20 h-20 object-cover rounded">
            <div class="flex-1">
              <h3 class="font-medium">{{ item.name }}</h3>
              <p class="text-gray-600">Qty: {{ item.quantity }}</p>
              <p class="text-primary font-medium">Total: {{ formatPrice(item.total_price) }}</p>
            </div>
            <a-button type="text" shape="circle" class="text-red-500">
              <template #icon><DeleteOutlined /></template>
            </a-button>
          </div>
        </div>
      </div>

      <div class="border-t p-4">
        <div class="flex justify-between mb-4">
          <span class="font-medium">Total:</span>
          <span class="font-bold text-primary">{{ formatPrice(total) }}</span>
        </div>
        <a-button type="primary" block @click="goToCheckout">
          Proceed to Checkout
        </a-button>
      </div>
    </div>
  </a-drawer>
</template>

<style scoped>
.ant-drawer-body {
  padding: 0;
  display: flex;
  flex-direction: column;
  height: 100%;
}
</style>
