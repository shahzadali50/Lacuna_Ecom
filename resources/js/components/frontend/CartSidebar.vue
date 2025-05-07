<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { ShoppingCartOutlined, DeleteOutlined } from '@ant-design/icons-vue';

defineProps<{
  visible: boolean;
}>();

const emit = defineEmits<{
  'update:visible': [value: boolean]
}>();

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

const goToCheckout = () => {
  router.visit(route('cart.view'));
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
  <a-drawer
    title="Shopping Cart"
    placement="right"
    :open="visible"
    @close="closeDrawer"
    :width="drawerWidth"
  >
    <div class="flex flex-col h-full">
      <div class="flex-1 overflow-y-auto">
        <div v-if="!cart || cart.length === 0" class="text-center py-8">
          <ShoppingCartOutlined class="text-4xl text-gray-400 mb-4" />
          <p class="text-gray-500">Your cart is empty</p>
        </div>
        <div v-else class="space-y-4">
          <div v-for="item in cart" :key="item.id" class="flex items-start gap-4 p-4 border-b">
            <img :src="'/storage/' + item.thumnail_img" :alt="item.name" class="w-20 h-20 object-cover rounded">
            <div class="flex-1">
              <div class="flex justify-between items-start">
                <h3 class="font-medium">{{ item.name }}</h3>
                <a-button type="text" shape="circle" class="text-red-500" @click="removeItem(item.id)">
                  <template #icon><DeleteOutlined /></template>
                </a-button>
              </div>
              <div class="flex items-center gap-2 mt-2">
                <a-button size="small" @click="updateQuantity(item.id, 'decrease')">-</a-button>
                <span class="text-gray-600">{{ item.quantity }}</span>
                <a-button size="small" @click="updateQuantity(item.id, 'add')">+</a-button>
              </div>
              <div class="flex justify-between items-start mt-2">
                <p>{{ item.quantity }} X {{ formatPrice(item.final_price) }}</p>
              </div>
              <p class="text-primary font-medium mt-2">Total: {{ formatPrice(item.total_price) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t p-4">
        <div class="flex justify-between mb-4">
          <span class="font-medium">Total:</span>
          <span class="font-bold text-primary">{{ formatPrice(total) }}</span>
        </div>
        <a-button type="primary" block @click="goToCheckout" :disabled="!cart || cart.length === 0">
          Proceed to Checkout
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
