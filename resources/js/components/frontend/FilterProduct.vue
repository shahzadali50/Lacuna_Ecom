
<script setup lang="ts">
import { ref, defineProps } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { Button, Collapse, CollapsePanel, Checkbox } from 'ant-design-vue';

import {
   ClearOutlined,
   FilterOutlined
} from '@ant-design/icons-vue';

interface Category {
  id: number;
  name: string;
  slug: string;
  product_count: number;
}

// Props
const props = defineProps<{
  categories?: Category[];
  selectedCategory?: string | null;
}>();

// Drawer state for filter sidebar
const isFilterDrawerVisible = ref(false);
const openFilterDrawer = () => { isFilterDrawerVisible.value = true; };
const closeFilterDrawer = () => { isFilterDrawerVisible.value = false; };

// Collapse state
const activeKey = ref(['1']);

// Filter by category
const filterByCategory = (categorySlug: string | null) => {
  router.get(
    route('all.products'),
    { category: categorySlug },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: () => {
        closeFilterDrawer();
      },
    }
  );
};
</script>

<template>
  <div>
    <Button class="flex items-center" size="large" @click="openFilterDrawer">
     <FilterOutlined />Filter Products
    </Button>
    <a-drawer
      title="Filter Products"
      placement="left"
      :open="isFilterDrawerVisible"
      @close="closeFilterDrawer"
    >
      <div>
       <div class="text-end">
        <Button size="small" type="primary">
          <Link :href="route('all.products')" class="hover:underline mb-4 flex items-center">
           <ClearOutlined />Clear Filters
          </Link>
        </Button>
       </div>
        <Collapse v-model:activeKey="activeKey" :bordered="false">
          <CollapsePanel key="1" header="Filter by Category">
            <div
              v-for="category in props.categories"
              :key="category.id"
              class="flex justify-between items-center mb-2 cursor-pointer"
              @click="filterByCategory(category.slug)"
            >
              <Checkbox :checked="props.selectedCategory === category.slug">
                {{ category.name }}
              </Checkbox>
              <p class="mb-0">
                {{ category.product_count }}Products
              </p>
            </div>
          </CollapsePanel>
        </Collapse>
      </div>
    </a-drawer>
  </div>
</template>

<style scoped>
</style>

