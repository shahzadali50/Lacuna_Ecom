<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { message } from 'ant-design-vue';
import { DeleteOutlined } from '@ant-design/icons-vue';

// ✅ State Management
const isLoading = ref(false);
const errors = ref<Record<string, string>>({});
const customerName = ref('');
const phoneNumber = ref('');
const paymentMethod = ref('');
const status = ref('completed');
const discount = ref<number>(0);
const quantity = ref<number>(1);
const orderItems = ref<OrderItem[]>([]);

// ✅ Props & Type Definition
interface PurchaseProduct {
    id: number;
    sale_price: number;
    stock: number;
    remaining_stock: number;
    description: string | null;
}

interface Product {
    id: number;
    name: string;
    purchase_products: PurchaseProduct[];
}

interface OrderItem {
    id: number;
    name: string;
    price: number;
    quantity: number;
    total: number;
}

const props = defineProps<{ products: Product[] }>();

// ✅ Selected Product & Purchase Product Management
const selectedProductId = ref<number | null>(props.products[0]?.id ?? null);
const selectedPurchaseProduct = ref<number | null>(null);

// ✅ Computed Properties
const selectedProduct = computed(() => props.products.find(p => p.id === selectedProductId.value));
const purchaseProducts = computed(() => selectedProduct.value?.purchase_products ?? []);
const selectedPurchaseProductData = computed(() => purchaseProducts.value.find(pp => pp.id === selectedPurchaseProduct.value));

const finalPrice = computed(() => selectedPurchaseProductData.value?.sale_price ?? 0);
const total = computed(() => finalPrice.value * quantity.value);
const totalAmount = computed(() => orderItems.value.reduce((sum, item) => sum + item.total, 0));
// ✅ Corrected Discount & Subtotal Calculation
const discountAmount = computed(() => (discount.value / 100) * totalAmount.value);
const subtotal = computed(() => totalAmount.value - discountAmount.value);

// ✅ Watchers
watch(selectedProductId, () => {
    selectedPurchaseProduct.value = purchaseProducts.value[0]?.id ?? null;
    quantity.value = 1;
});

watch(discount, () => {
    if (discount.value < 0) discount.value = 0;
    if (discount.value > 100) discount.value = 100;
});

// ✅ Add Product to Order
const addToOrder = () => {
    if (!selectedProduct.value || !selectedPurchaseProductData.value) return;

    const existingItem = orderItems.value.find(item => item.id === selectedPurchaseProductData.value.id);
    if (existingItem) {
        existingItem.quantity += quantity.value;
        existingItem.total = existingItem.quantity * existingItem.price;
    } else {
        orderItems.value.push({
            id: selectedPurchaseProductData.value.id,
            name: selectedProduct.value.name,
            price: finalPrice.value,
            quantity: quantity.value,
            total: total.value,
        });
    }

    // Reset selections
    selectedProductId.value = props.products[0]?.id ?? null;
    selectedPurchaseProduct.value = null;
    quantity.value = 1;
};

// ✅ Remove Product from Order
const removeItem = (id: number) => {
    orderItems.value = orderItems.value.filter(item => item.id !== id);
};

// ✅ Submit Order
const submitOrder = () => {
    if (orderItems.value.length === 0) {
        message.error('Please add at least one product to the order.');
        return;
    }

    isLoading.value = true;

    const payload = {
        name: customerName.value,
        phone_number: phoneNumber.value,
        total_price: totalAmount.value,
        discount: discount.value,
        subtotal_price: subtotal.value,
        status: status.value,
        payment_method: paymentMethod.value,
        products: orderItems.value.map(item => ({
            product_id: item.id,
            purchase_id: item.id,
            qty: item.quantity,
            sale_price: item.price,
            total_price: item.total,
        })),
    };

    router.post(route('admin.order.store'), payload, {
        onSuccess: () => {
            orderItems.value = [];
            customerName.value = '';
            phoneNumber.value = '';
            discount.value = 0;
        },
        onError: (error) => {
            console.error(error);
            if (error) {
                errors.value = error;
            }
            message.error('Failed to create order. Please check inputs.');
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
</script>

<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>

        <Head title="Create Order" />

        <a-row class="bg-white rounded-lg p-4 shadow-md gap-2">
            <a-col :xs="24" :lg="24">
                <h1 class="text-2xl">Create Order</h1>

            </a-col>
            <a-col :xs="24" :sm="12" :lg="5">
                <div class="mb-4">
                    <label class="block">Select Product</label>
                    <a-select v-model:value="selectedProductId" show-search placeholder="Search and Select Product"
                        option-filter-prop="label" style="width: 100%"
                        :options="props.products.map(p => ({ value: p.id, label: p.name }))" />
                </div>
            </a-col>

            <!-- Select Sale Price -->
            <a-col :xs="24" :sm="11" :lg="5">
                <div class="mb-4">
                    <label class="block">Select Sale Price</label>
                    <a-select v-model:value="selectedPurchaseProduct" placeholder="Price" style="width: 100%"
                        :options="purchaseProducts.map(pp => ({ value: pp.id, label: `Sale Price: ${pp.sale_price} (Stock: ${pp.remaining_stock})` }))" />
                </div>
            </a-col>

            <!-- Final Price -->
            <a-col :xs="24" :sm="12" :lg="3">
                <div class="mb-4">
                    <label class="block">Final Price</label>
                    <a-input type="number" v-model:value="finalPrice" class="w-full" placeholder="Final Price" />
                </div>
            </a-col>

            <!-- Quantity -->
            <a-col :xs="24" :sm="11" :lg="2">
                <div class="mb-4">
                    <label>QTY</label>
                    <a-input min="1" type="number" v-model:value="quantity" class="w-full"
                        placeholder="Enter Quantity" />
                </div>
            </a-col>

            <!-- Total Price -->
            <a-col :xs="24" :sm="12" :lg="4">
                <div class="mb-4">
                    <label>Total</label>
                    <a-input readonly :value="total" class="w-full" placeholder="Total Price" />
                </div>
            </a-col>

            <!-- Action Button -->
            <a-col :xs="24" :sm="11" :lg="2">
                <div class="mb-4">
                    <label class="block">Action</label>
                    <a-button type="primary" block @click="addToOrder">ADD</a-button>
                </div>
            </a-col>
        </a-row>


        <a-row class="mt-4 justify-between">
            <a-col :xs="24" :lg="15">
                <div class="bg-white rounded-lg p-4 shadow-md mb-2 overflow-x-auto ">
                    <table class="table-auto w-full ">
                        <thead>
                            <tr class="text-left">
                                <th>Sr</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>QTY</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in orderItems" :key="index">
                                <td class="py-2">{{ index + 1 }}</td>
                                <td class="py-2">{{ item.name }}</td>
                                <td class="py-2">{{ item.price }}</td>
                                <td class="py-2">{{ item.quantity }}</td>
                                <td class="py-2">{{ item.total }}</td>
                                <td class="py-2">
                                    <a-tooltip placement="top">
                                        <template #title>Delete</template>
                                        <a-button type="text" class="text-red-700 hover:text-red-800"
                                            @click="removeItem(item.id)">
                                            <DeleteOutlined />
                                        </a-button>
                                    </a-tooltip>


                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </a-col>

            <a-col :xs="24" :lg="8">
                <div class="bg-white rounded-lg p-4 shadow-md ml-lg-2">
                    <a-row>
                        <a-col :xs="24">
                            <div class="bg-gray-300 p-2 mb-4">
                                <h3 class="text-2xl">Order Summary</h3>

                            </div>

                        </a-col>
                        <a-col :sm="12" :xs="24">
                            <div class="mb-4 mx-1">
                                <label class="block">Name</label>
                                <a-input v-model:value="customerName" class="mt-2 w-full" placeholder="Name" />
                                <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                                <!-- ✅ Directly display the error -->
                            </div>

                        </a-col>
                        <a-col :sm="12" :xs="24">
                            <div class="mb-4 mx-1">
                                <label class="block">Phone Number</label>
                                <a-input type="number" v-model:value="phoneNumber" class="mt-2 w-full"
                                    placeholder="Phone Number" />
                                <div v-if="errors.phone_number" class="text-red-500">{{ errors.phone_number }}</div>
                                <!-- ✅ Directly display the error -->
                            </div>

                        </a-col>
                        <a-col :xs="24">
                            <div>
                                <h4 class="mb-2">Subtotal: {{ subtotal }}</h4>
                                <div class="flex items-center mb-2">
                                    <h4>Discount ({{ discount }}%):</h4>
                                    <a-input min="0" max="100" type="number" v-model:value="discount" class="w-52 ml-3"
                                        placeholder="Discount (%)" />
                                </div>
                                <h4 class="mb-2">Total: {{ total }}</h4>
                            </div>
                            <a-button type="primary" class="w-full" @click="submitOrder">Generate Order</a-button>
                        </a-col>
                    </a-row>
                </div>

            </a-col>
        </a-row>
    </AdminLayout>
</template>
