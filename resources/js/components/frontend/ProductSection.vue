<script setup lang="ts">
import { ref, computed, defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ShoppingCartOutlined, HeartOutlined } from '@ant-design/icons-vue';
import { Row, Col, Card, Button } from 'ant-design-vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import PaginationComponent from '@/components/frontend/PaginationComponent.vue';

interface Product {
  id: number;
  name: string;
  slug: string;
  final_price: number;
  sale_price: number;
  thumnail_img: string;
  category_name: string;
}

// Props
const props = defineProps<{
  title?: string;
  subtitle?: string;
  showAllProductsButton?: boolean;
  showPagination?: boolean;
}>();

const page = usePage();

const translations = computed(() => {
  return (page.props.translations as any)?.products || {};
});

// Drawer state for filter sidebar
const isFilterDrawerVisible = ref(false);
const openFilterDrawer = () => { isFilterDrawerVisible.value = true; };
const closeFilterDrawer = () => { isFilterDrawerVisible.value = false; };

// Products
const products = ref<Product[]>((page.props.products as any)?.data || []);

// Format price
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
  }).format(price);
};

// Product detail navigation
const goToProductDetail = (slug: string) => {
  router.visit(route('product.detail', { slug }));
};

// Add to cart
const addingToCart = ref(new Set<number>());
const addToCart = (product: Product) => {
  addingToCart.value.add(product.id);
  router.post(
    route('cart.add'),
    {
      id: product.id,
      qty: 1,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        addingToCart.value.delete(product.id);
      },
      onError: (errors) => {
        addingToCart.value.delete(product.id);
        console.error('Failed to add to cart', errors);
      },
    }
  );
};
</script>

<template>
  <section class="py-14">
    <div class="container mx-auto px-2 sm:px-4">
      <!-- Filter Button and Drawer -->
      <div>
        <a-button size="large" @click="openFilterDrawer">
           Show Filter
        </a-button>
        <a-drawer
          title="Filter Products"
          placement="left"
          :open="isFilterDrawerVisible"
          @close="closeFilterDrawer"
        >
          <!-- Add your filter form or content here -->
          <div>
            <p>Filter options go here.</p>
          </div>
        </a-drawer>
      </div>

      <!-- Heading and Subtitle -->
      <div class="text-center mb-8 sm:mb-12">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">
          {{ props.title || translations.title || 'Product List' }}
        </h2>
        <p class="text-base sm:text-lg text-gray-600 max-w-3xl mx-auto">
          {{ props.subtitle || translations.subtitle || 'Explore our wide range of high-quality products tailored to your needs.' }}
        </p>
      </div>

      <!-- Product Cards -->
      <Row :gutter="[16, 16]">
        <Col
          :xs="12"
          :sm="12"
          :md="8"
          :lg="8"
          :xl="6"
          v-for="product in products"
          :key="product.id"
          class="mb-6"
        >
          <Card hoverable class="h-full product-card cursor-pointer" @click="goToProductDetail(product.slug)">
            <template #cover>
              <div class="relative h-28 sm:h-32 md:h-40 lg:h-48 overflow-hidden">
                <img
                  :src="'/storage/' + product.thumnail_img"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
                <div
                  class="absolute top-1 right-1 bg-white rounded-full px-1.5 py-0.5 text-[10px] sm:text-xs font-medium text-gray-800"
                >
                  {{ product.category_name }}
                </div>
                <Button
                  type="primary"
                  shape="circle"
                  size="small"
                  class="flex items-center justify-center bg-danger hover:!bg-pink-700 !w-6 !h-6 absolute top-7 right-1 bg-white"
                  aria-label="Add to favorites"
                >
                  <template #icon><HeartOutlined /></template>
                </Button>
              </div>
            </template>
            <div>
              <h3 class="text-[15px] sm:text-xl font-semibold text-gray-900 mb-1">
                {{ product.name }}
              </h3>
              <div class="flex justify-between items-center">
                <div class="flex flex-wrap">
                  <span class="text-xs sm:text-sm md:text-base font-bold text-primary pr-2">
                    {{ formatPrice(product.final_price) }}
                  </span>
                  <span class="text-xs sm:text-sm md:text-base text-secondary line-through">
                    {{ formatPrice(product.sale_price) }}
                  </span>
                </div>
              </div>
              <Button
                type="primary"
                class="btn-primary flex items-center justify-center mt-2"
                :loading="addingToCart.has(product.id)"
                @click.stop="addToCart(product)"
                aria-label="Add to cart"
              >
                <template #icon><ShoppingCartOutlined /></template>
                {{ translations.add_to_cart || 'Add to Cart' }}
              </Button>
            </div>
          </Card>
        </Col>
      </Row>

      <!-- Pagination -->
      <PaginationComponent
        v-if="props.showPagination !== false"
        :pagination-data="page.props.products"
        route-name="all.products"
      />

      <!-- All Products Button -->
      <div v-if="props.showAllProductsButton === true" class="text-center mt-8 sm:mt-12">
        <Button size="large" class="btn-primary" aria-label="View all products">
          <Link :href="route('all.products')">
            {{ translations.all_products || 'All Products' }}
          </Link>
        </Button>
      </div>
    </div>
  </section>
</template>

<style scoped>
.btn-primary {
  background-color: #1890ff;
  color: white;
  border: none;
}
.btn-primary:hover {
  background-color: #40a9ff;
}
</style>
