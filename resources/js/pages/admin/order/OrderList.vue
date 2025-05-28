<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import dayjs from "dayjs";
import { ref, computed } from "vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

DataTable.use(DataTablesCore);

const isLoading = ref(false);

const page = usePage();
const translations = computed(() => {
    return (page.props.translations as any)?.dashboard_all_pages || {};
});

const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

defineProps({
    orders: Object,
});

const isOrderViewModalVisible = ref(false);
const selectedOrder = ref<any>(null);

const openOrderView = (order: any) => {
    selectedOrder.value = order;
    isOrderViewModalVisible.value = true;
};

const getStatusColor = (status: string) => {
    switch (status?.toLowerCase()) {
        case 'pending':
            return 'orange';
        case 'processing':
            return 'blue';
        case 'completed':
            return 'green';
        case 'cancelled':
            return 'red';
        default:
            return 'default';
    }
};

// DataTable columns for orders
const dataTableColumns = [
    {
        title: translations.value.sr || 'Sr.',
        data: null,
        render: (data: any, type: any, row: any, meta: any) => meta.row + 1,
    },
    {
        title: translations.value.order_id || 'Order ID',
        data: 'order_id',
        render: (data: string) => `<span class="badge bg-primary">${data}</span>`,
    },
    {
        title: translations.value.status || 'Status',
        data: 'status',
        render: (data: string) => {
            const color = getStatusColor(data);
            return `<span class="badge bg-${color}">${data}</span>`;
        }
    },
    {
        title: translations.value.customer_name || 'Customer Name',
        data: 'name'
    },
    {
        title: translations.value.phone_number || 'Phone No',
        data: 'phone_number'
    },
    {
        title: translations.value.subtotal || 'SubTotal',
        data: 'subtotal_price',
        render: (data: number) => Math.floor(data)
    },
    {
        title: translations.value.total || 'Total',
        data: 'total_price',
        render: (data: number) => Math.floor(data)
    },
    {
        title: translations.value.created_at || 'Created At',
        data: 'created_at',
        render: (data: string) => formatDate(data)
    },
    {
        title: translations.value.action || 'Action',
        data: null,
        orderable: false,
        render: (data: any, type: any, row: any) => `
            <button class="view-btn p-2" data-id="${row.id}" title="${translations.value.view || 'View'}">
                 <i class="fa fa-cart-plus text-2xl" aria-hidden="true"></i>
            </button>
        `
    }
];

// DataTable options
const options = {
    paging: true,
    searching: true,
    ordering: true,
    responsive: true,
    pageLength: 10,
    createdRow: (row: HTMLElement, data: any) => {
        setTimeout(() => {
            row.querySelector('.view-btn')?.addEventListener('click', () => openOrderView(data));
        }, 0);
    },
};
</script>

<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>
        <Head :title="translations.order_list || 'Order List'" />
        <a-row>


            <a-col :xs="24">

                <div class="bg-white rounded-lg responsive-table p-4 shadow-md">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold mb-4">{{ translations.order_list || 'Order List' }}</h2>
                    </div>
                    <DataTable
                        v-if="orders?.data"
                        :data="orders.data"
                        :columns="dataTableColumns"
                        :options="options"
                        class="display"
                    >
                        <thead>
                            <tr>
                                <th>{{ translations.sr || 'Sr.' }}</th>
                                <th>{{ translations.order_id || 'Order ID' }}</th>
                                <th>{{ translations.status || 'Status' }}</th>
                                <th>{{ translations.customer_name || 'Customer Name' }}</th>
                                <th>{{ translations.phone || 'Phone No' }}</th>
                                <th>{{ translations.subtotal || 'SubTotal' }}</th>
                                <th>{{ translations.total || 'Total' }}</th>
                                <th>{{ translations.created_at || 'Created At' }}</th>
                                <th>{{ translations.action || 'Action' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this automatically -->
                        </tbody>
                    </DataTable>
                </div>
            </a-col>
        </a-row>

        <!-- Order View Modal -->
        <a-modal
            width="700px"
            v-model:open="isOrderViewModalVisible"
            :title="translations.order_preview || 'Order Preview'"
            @cancel="isOrderViewModalVisible = false"
            :footer="null"
        >
            <a-row>
                <a-col :xs="24">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-3">{{ translations.shiping_details || 'Shipping Details' }}</h3>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="12">
                                <p class="mb-2"><span class="font-semibold">{{ translations.name || 'Name' }}:</span> {{ selectedOrder?.name }}</p>
                                <p class="mb-2"><span class="font-semibold">{{ translations.phone_number || 'Phone' }}:</span> {{ selectedOrder?.phone_number }}</p>
                                <p class="mb-2"><span class="font-semibold">{{ translations.email || 'Email' }}:</span> {{ selectedOrder?.email }}</p>
                            </a-col>
                            <a-col :xs="24" :sm="12">
                                <p class="mb-2"><span class="font-semibold">{{ translations.address || 'Address' }}:</span> {{ selectedOrder?.address }}</p>
                                <p class="mb-2"><span class="font-semibold">{{ translations.city || 'City' }}:</span> {{ selectedOrder?.city }}</p>
                                <p class="mb-2"><span class="font-semibold">{{ translations.state || 'State' }}:</span> {{ selectedOrder?.state }}</p>
                                <p class="mb-2"><span class="font-semibold">{{ translations.postal_code || 'Postal Code' }}:</span> {{ selectedOrder?.postal_code }}</p>
                                <p class="mb-2"><span class="font-semibold">{{ translations.country || 'Country' }}:</span> {{ selectedOrder?.country }}</p>
                            </a-col>
                        </a-row>
                        <div v-if="selectedOrder?.order_notes" class="mt-2">
                            <p class="mb-2"><span class="font-semibold">{{ translations.order_notes || 'Order Notes' }}:</span> {{ selectedOrder.order_notes }}</p>
                        </div>
                    </div>
                </a-col>
                <a-col :xs="24">
                    <div class="mb-2 overflow-x-auto">
                        <div class="border-gray-500 border my-4"></div>
                        <table class="table-auto w-full min-w-[600px]">
                            <thead>
                                <tr class="text-left">
                                    <th>{{ translations.sr || 'Sr' }}</th>
                                    <th>{{ translations.image || 'Image' }}</th>
                                    <th>{{ translations.product || 'Product' }}</th>
                                    <th>{{ translations.price || 'Price' }}</th>
                                    <th>{{ translations.quantity || 'QTY' }}</th>
                                    <th>{{ translations.total || 'Total' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(sale, index) in selectedOrder?.sale_products" :key="sale.id" class="border-b py-3">
                                    <td class="py-2">{{ index + 1 }}</td>
                                    <td class="py-2">
                                        <img
                                            :src="'/storage/' + sale.product.thumnail_img"
                                            :alt="sale.product.name"
                                            class="w-16 h-16 object-cover rounded"
                                        />
                                    </td>
                                    <td class="py-2">{{ sale.product.name }}</td>
                                    <td class="py-2">{{ sale.sale_price }}</td>
                                    <td class="py-2">{{ sale.qty }}</td>
                                    <td class="py-2">{{ sale.total_price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </a-col>
                <a-col :xs="24">
                    <div class="border-gray-500 border my-4"></div>
                    <div class="my-3">
                        <h4 class="mb-2">{{ translations.subtotal || 'Subtotal' }}: <span class="font-bold text-primary">{{ selectedOrder?.subtotal_price }}</span></h4>
                        <h4 class="mb-2">{{ translations.shipping_charge || 'Shipping Charges' }}: <span class="font-bold text-primary">{{ translations.free_delivery || 'Free Delivery' }}</span></h4>
                        <h4 class="mb-2">{{ translations.total_price || 'Total Price' }}: <span class="font-bold text-primary">{{ selectedOrder?.total_price }}</span></h4>
                    </div>
                </a-col>
            </a-row>
        </a-modal>
    </AdminLayout>
</template>

<style scoped>
.display {
    width: 100%;
}

.badge {
    padding: 0.5em 0.75em;
    border-radius: 0.25rem;
    color: white;
}

.bg-primary {
    background-color: #1890ff;
}

.bg-orange {
    background-color: #fa8c16;
}

.bg-blue {
    background-color: #1890ff;
}

.bg-green {
    background-color: #52c41a;
}

.bg-red {
    background-color: #f5222d;
}

.view-btn {
    color: #1890ff;
    text-decoration: none;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
}

.view-btn:hover {
    color: #40a9ff;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
</style>
