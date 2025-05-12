<script setup lang="ts">
import { ref,computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ShoppingCartOutlined, HeartOutlined } from '@ant-design/icons-vue';
import { Row, Col, Card, Button } from 'ant-design-vue';
import { router } from '@inertiajs/vue3';



interface Product {
  id: number;
  name: string;
  slug: string;
  final_price: number;
  sale_price: number;
  thumnail_img: string;
  category_name: string;
}
const page = usePage();

const translations = computed(() => {
    return (page.props.translations as any)?.products || {};
});
// Get products from props
const products = ref<Product[]>((page.props.products as any)?.data || []);

// Format price with currency symbol
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2
  }).format(price);
};

// Navigate to product detail page
const goToProductDetail = (slug: string) => {
  router.visit(route('product.detail', { slug }));
};

// Add to cart function
const addToCart = (product: Product) => {
  router.post(route('cart.add'), {
    id: product.id,
    qty: 1,
  }, {
    preserveScroll: true,
    onSuccess: () => {
    //   console.log('Added to cart');
    },
    onError: (errors) => {
      console.error('Failed to add to cart', errors);
    }
  });
};




// Add to favorites function
const addToFavorites = (e: Event, productId: number) => {
  e.stopPropagation(); // Prevent triggering the card click
  // In a real app, this would add the product to favorites
  console.log(`Adding product ${productId} to favorites`);
};
</script>

<template>
  <section class="py-14">
    <div class="container mx-auto px-2 sm:px-4">
      <div class="text-center mb-8 sm:mb-12">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">{{ translations.title || 'Product List' }}</h2>
        <p class="text-base sm:text-lg text-gray-600 max-w-3xl mx-auto">{{ translations.subtitle || '' }}</p>
      </div>

      <Row :gutter="[8, 8]">
        <Col :xs="12" :sm="12" :md="8" :lg="8" :xl="6" v-for="product in products" :key="product.id" class="mb-6">
          <Card
            hoverable
            class="h-full product-card cursor-pointer"
            @click="goToProductDetail(product.slug)"
          >
            <template #cover>
              <div class="relative h-28 sm:h-32 md:h-40 lg:h-48 overflow-hidden">
                <img :src="'/storage/' + product.thumnail_img" :alt="product.name" class="w-full h-full object-cover">
                <div class="absolute top-1 right-1 bg-white rounded-full px-1.5 py-0.5 text-[10px] sm:text-xs font-medium text-gray-800">
                  {{ product.category_name }}

                </div>
              </div>
            </template>
            <div class="">
              <h3 class="text-[15px] sm:text-xl font-semibold text-gray-900 mb-1">{{ product.name }}</h3>
              <div class="flex justify-between items-center">
               <div class="flex flex-wrap">
                <span class="text-xs sm:text-sm md:text-base font-bold text-primary pr-2">{{ formatPrice(product.final_price) }}</span>
                <span class="text-xs sm:text-sm md:text-base text-secondary line-through">{{ formatPrice(product.sale_price) }}</span>
               </div>
              </div>
                <div class="flex gap-1 mt-2">
                    <Button
                        type="primary"
                        shape="circle"
                        size="small"
                        class="flex items-center justify-center bg-primary !w-6 !h-6"
                        @click.stop="addToCart(product)"
                        >
                        <template #icon><shopping-cart-outlined /></template>
                        </Button>

                  <Button
                    type="primary"
                    shape="circle"
                    size="small"
                    class="flex items-center justify-center bg-danger hover:!bg-pink-700 !w-6 !h-6"
                    @click="(e) => addToFavorites(e, product.id)"
                  >
                    <template #icon><heart-outlined /></template>
                  </Button>
                </div>
            </div>
          </Card>
        </Col>
      </Row>

      <div class="text-center mt-8 sm:mt-12">
        <Button size="middle" class="btn-primary">
          <a href="/marketplace">{{ translations.view_all || 'View All' }}</a>
        </Button>
      </div>
    </div>
  </section>
</template>

