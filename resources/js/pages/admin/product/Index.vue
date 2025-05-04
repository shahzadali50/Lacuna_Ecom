<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import dayjs from "dayjs";
import { ref, computed } from 'vue';
import AddProduct from '@/components/admin/product/AddProduct.vue';
import EditProduct from '@/components/admin/product/EditProduct.vue';

// 1. Type Declarations
declare const URL: {
    createObjectURL(file: File): string;
};

// 2. Props Definition
const props = defineProps<{
    products: { data: Array<any>; current_page: number; per_page: number; total: number };
    brands: Array<{ id: number; name: string; category_id: number }>;
    categories: Array<{ id: number; name: string }>;
}>();

// 3. State Management
const isLoading = ref(false);
const isAddProductModalVisible = ref(false);
const isEditModalVisible = ref(false);
const selectedProduct = ref<{
    id: number;
    name: string;
    description: string;
    brand_id: number;
    category_id: number;
    thumnail_img: string;
    gallary_img: string;
    stock: number;
    status: string;
    purchase_price: number;
    sale_price: number;
    feature: boolean;
    barcode: string;
    discount: number;
    final_price: number;
} | null>(null);

// 4. Page and Translations
const page = usePage();
const translations = computed(() => {
    return (page.props.translations as any)?.dashboard_all_pages || {};
});


// 6. Computed Properties
const columns = computed(() => [
    { title: translations.value.id || 'ID', dataIndex: 'id', key: 'id' },
    { title: translations.value.image || 'Image', dataIndex: 'image', key: 'image' },
    { title: translations.value.name || 'Name', dataIndex: 'name', key: 'name' },
    { title: translations.value.stock || 'Stock', dataIndex: 'stock', key: 'stock' },
    { title: translations.value.purchase_price || 'Purchase Price', dataIndex: 'purchase_price', key: 'purchase_price' },
    { title: translations.value.sale_price || 'Sale Price', dataIndex: 'sale_price', key: 'sale_price' },
    { title: translations.value.discount || 'Discount', dataIndex: 'discount', key: 'discount' },
    { title: translations.value.final_price || 'Final Price', dataIndex: 'final_price', key: 'final_price' },
    { title: translations.value.category || 'Category', dataIndex: 'category_name', key: 'category_name' },
    { title: translations.value.brand || 'Brand', dataIndex: 'brand_name', key: 'brand_name' },
    { title: translations.value.created_at || 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: translations.value.action || 'Action', dataIndex: 'action', key: 'action' },
]);
// 7. Helper Functions
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};



// 8. Event Handlers
const deleteProduct = (id: number) => {
    Modal.confirm({
        title: translations.value.delete_product_confirm || 'Are you sure you want to delete this Product?',
        content: translations.value.delete_product_warning || 'This action cannot be undone.',
        okText: 'Yes, Delete',
        okType: 'danger',
        cancelText: 'Cancel',
        onOk() {
            isLoading.value = true;
            useForm({}).delete(route('admin.product.delete', { id: id }), {
                onSuccess: () => {
                },
                onFinish: () => {
                    isLoading.value = false;
                }
            });
        },
    });
};

const openEditModal = (product: any) => {
    selectedProduct.value = product;
    isEditModalVisible.value = true;
};


</script>

<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>

        <Head title="Product List" />
        <a-row>
            <a-col :span="24">
                <div class="bg-white rounded-lg p-4 shadow-md responsive-table">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">{{ translations.product_list || 'Product List ' }}</h2>

                        <div>
                            <a-button class="mx-2" type="default" @click="isAddProductModalVisible = true">
                                {{ translations.add_product || 'Add Product' }}
                            </a-button>
                            <Link :href="route('admin.product-log')">
                            <a-button class="mx-2" type="default">{{ translations.product_logs || 'Product Logs' }}</a-button>
                            </Link>
                        </div>
                    </div>

                    <!-- Display table -->
                    <a-table v-if="products" :columns="columns" :data-source="products.data" rowKey="id" bordered>
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-if="column.dataIndex === 'image'">

                                <div>
                                    <img v-if="record.thumnail_img" :src="'/storage/' + record.thumnail_img"
                                        alt="thumnail_img"
                                        class="w-12 h-12 object-cover rounded mb-1 cursor-pointer hover:opacity-80 transition-opacity" />
                                    <span v-else class="text-gray-400 mb-1">No Image</span>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-if="column.dataIndex === 'stock'">
                                {{ record.stock }}
                            </template>
                            <template v-if="column.dataIndex === 'purchase_price'">
                                {{ record.purchase_price }}
                            </template>
                            <template v-if="column.dataIndex === 'sale_price'">
                                {{ record.sale_price }}
                            </template>
                            <template v-if="column.dataIndex === 'discount'">
                                {{ record.discount ? record.discount + '%' : '0%' }}
                            </template>
                            <template v-if="column.dataIndex === 'final_price'">
                                {{ record.final_price || record.sale_price }}
                            </template>
                            <template v-if="column.dataIndex === 'category_name'">
                                {{ record.category_name }}
                            </template>
                            <template v-if="column.dataIndex === 'brand_name'">
                                {{ record.brand_name }}
                            </template>
                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.delete || 'Delete' }}</template>
                                    <a-button type="link" @click="deleteProduct(record.id)"><i
                                            class="fa fa-trash text-red-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.edit || 'Edit' }}</template>
                                    <a-button type="link" @click="openEditModal(record)"><i
                                            class="fa fa-pencil-square-o text-s text-green-500"
                                            aria-hidden="true"></i></a-button>
                                </a-tooltip>


                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>

        <AddProduct :is-visible="isAddProductModalVisible" :categories="categories" :brands="brands"
            :translations="translations" @update:is-visible="isAddProductModalVisible = $event"
            @success="isAddProductModalVisible = false" />

        <EditProduct :is-visible="isEditModalVisible" :product="selectedProduct" :categories="categories"
            :brands="brands" :translations="translations" @update:is-visible="isEditModalVisible = $event"
            @success="isEditModalVisible = false" />


    </AdminLayout>
</template>

<style scoped>

</style>
