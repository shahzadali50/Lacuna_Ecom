<script setup lang="ts">
import { Head,usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import {computed } from 'vue';
import dayjs from "dayjs";
import { ref } from "vue";
import { FundViewOutlined } from '@ant-design/icons-vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

const columns = [
    { title: 'Order ID', dataIndex: 'order_id', key: 'order_id' },
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Phone No', dataIndex: 'phone_number', key: 'phone_number' },
    { title: 'Status', dataIndex: 'status', key: 'status' },
    { title: 'Payment Method', dataIndex: 'payment_method', key: 'payment_method' },
    { title: 'Total', dataIndex: 'total_price', key: 'total_price' },
    { title: 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: 'Action', dataIndex: 'action', key: 'action' },
];



defineProps({
    orders: Object,
});
const isOrderViewModalVisible = ref(false);
const selectedOrder = ref<any>(null);

const openOrderView = (order: any) => {
    selectedOrder.value = order;
    isOrderViewModalVisible.value = true;
};

const page = usePage<SharedData>();
const translations = computed(() => page.props.translations as Record<string, string>);


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Order Details',
        href: '/order/info',
    },
];




</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Order Details" />

        <div class="container">
            <a-row>
            <a-col :span="24">
                <div class=" p-4 sresponsive-table">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Order List</h2>
                    </div>

                    <a-table v-if="orders" :columns="columns" :data-source="orders.data" rowKey="id">
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'order_id'">
                                {{ record.order_id }}
                            </template>
                            <template v-else-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-else-if="column.dataIndex === 'phone_number'">
                                {{ record.phone_number }}
                            </template>
                            <template v-else-if="column.dataIndex === 'status'">
                                <a-tag :color="record.status === 'completed' ? 'green' : 'blue'">
                                    {{ record.status || 'Pending' }}
                                </a-tag>
                            </template>
                            <template v-else-if="column.dataIndex === 'payment_method'">
                                {{ record.payment_method }}
                            </template>
                            <template v-else-if="column.dataIndex === 'total_price'">
                                {{ Math.floor(record.total_price) }}
                            </template>
                            
                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-tooltip placement="top">
                                    <template #title>Order View</template>
                                    <a-button type="link" @click="openOrderView(record)"><FundViewOutlined /></a-button>
                                </a-tooltip>
                            </template>
                        </template>
                    </a-table>

                </div>
            </a-col>
        </a-row>
        <!-- Edit Product Modal -->
        <a-modal width="800px" v-model:open="isOrderViewModalVisible" title="Order Details"
            @cancel="isOrderViewModalVisible = false" :footer="null">
            <div class="bg-white rounded-lg">
                <!-- Order Header -->
                <div class="border-b pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Order #{{ selectedOrder?.order_id }}</h2>
                            <p class="text-gray-500">{{ formatDate(selectedOrder?.created_at) }}</p>
                        </div>
                        <a-tag :color="selectedOrder?.status === 'completed' ? 'success' : 'processing'" class="text-sm">
                            {{ selectedOrder?.status || 'Pending' }}
                        </a-tag>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Left Column - Shipping Info -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Shipping Information</h3>
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="text-gray-600 w-24">Name:</span>
                                    <span class="font-medium">{{ selectedOrder?.name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">Email:</span>
                                    <span class="font-medium">{{ selectedOrder?.email }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">Phone:</span>
                                    <span class="font-medium">{{ selectedOrder?.phone_number }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">Address:</span>
                                    <span class="font-medium">{{ selectedOrder?.address }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">City:</span>
                                    <span class="font-medium">{{ selectedOrder?.city }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">State:</span>
                                    <span class="font-medium">{{ selectedOrder?.state }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">Country:</span>
                                    <span class="font-medium">{{ selectedOrder?.country }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-24">Postal Code:</span>
                                    <span class="font-medium">{{ selectedOrder?.postal_code }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="selectedOrder?.order_notes" class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Order Notes</h3>
                            <p class="text-gray-700">{{ selectedOrder?.order_notes }}</p>
                        </div>
                    </div>

                    <!-- Right Column - Order Items & Summary -->
                    <div class="space-y-4">
                        <!-- Order Items -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Order Items</h3>
                            <div class="space-y-3">
                                <div v-for="(sale, index) in selectedOrder?.sale_products" :key="sale.id"
                                    class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-gray-500">#{{ index + 1 }}</span>
                                        <div>
                                            <p class="font-medium">{{ sale.product.name }}</p>
                                            <p class="text-sm text-gray-500">Qty: {{ sale.qty }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">${{ sale.sale_price }}</p>
                                        <p class="text-sm text-gray-500">Total: ${{ sale.total_price }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Order Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">${{ selectedOrder?.subtotal_price }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="font-medium">{{ Math.floor(selectedOrder?.discount) }}%</span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between text-lg">
                                        <span class="font-semibold">Total</span>
                                        <span class="font-bold">${{ selectedOrder?.total_price }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a-modal>

        </div>


    </AppLayout>
</template>
