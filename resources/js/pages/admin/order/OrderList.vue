<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import dayjs from "dayjs";
import { ref } from "vue";
import { FundViewOutlined } from '@ant-design/icons-vue';
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

const columns = [
    { title: 'ID', dataIndex: 'id', key: 'id' },
    { title: 'Customer Name', dataIndex: 'name', key: 'name' },
    { title: 'Phone No', dataIndex: 'phone_no', key: 'phone_no' },
    { title: 'SubTotal', dataIndex: 'subtotal', key: 'subtotal' },
    { title: 'Discount (%)', dataIndex: 'discount', key: 'discount' },
    { title: 'Total', dataIndex: 'total', key: 'total' },
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
</script>

<template>
    <AdminLayout>

        <Head title="Order List" />
        <a-row>
            <a-col :span="24">
                <div class="bg-white rounded-lg p-4 shadow-md responsive-table">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Order List</h2>
                    </div>

                    <a-table v-if="orders" :columns="columns" :data-source="orders.data" rowKey="id">
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-else-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-else-if="column.dataIndex === 'phone_no'">
                                {{ record.phone_number }}
                            </template>
                            <template v-else-if="column.dataIndex === 'subtotal'">

                                {{ Math.floor(record.subtotal_price) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'discount'">
                                {{ Math.floor(record.discount) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'total'">
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
        <a-modal width="700px" v-model:open="isOrderViewModalVisible" title="Order Preview"
            @cancel="isOrderViewModalVisible = false" :footer="null">
            <a-row>
                <a-col :xs="24">
                    <div class="mb-2 overflow-x-auto">
                    <div class="border-gray-500 border  my-4">
                    </div>
                        <table class="table-auto w-full">
                            <thead>
                                <tr class="text-left">
                                    <th>Sr</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>QTY</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(sale, index) in selectedOrder?.sale_products" :key="sale.id">
                                    <td class="py-2">{{ index + 1 }}</td>
                                    <td class="py-2">{{ sale.product.name  }}</td>
                                    <td class="py-2">{{ sale.sale_price }}</td>
                                    <td class="py-2">{{ sale.qty }}</td>
                                    <td class="py-2">{{ sale.total_price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </a-col>
                <a-col  :xs="24">
                    <div class="border-gray-500 border  my-4">
                    </div>
                    <div class="my-3">
                        <h4 class="mb-2">Subtotal: {{ selectedOrder?.subtotal_price }}</h4>
                        <div class="flex items-center mb-2">
                            <h4>Discount: {{ Math.floor(selectedOrder?.discount) }}%</h4>
                        </div>
                        <h4 class="mb-2">Total: {{ selectedOrder?.total_price }}</h4>
                    </div>
                </a-col>
            </a-row>
        </a-modal>

    </AdminLayout>
</template>
