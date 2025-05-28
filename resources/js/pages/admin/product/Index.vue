<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import dayjs from 'dayjs';
import { ref, computed } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

DataTable.use(DataTablesCore);

const isLoading = ref(false);

const page = usePage();
const translations = computed(() => {
  return (page.props.translations as any)?.dashboard_all_pages || {};
});

const props = defineProps<{
  products: { data: Array<any> };
  brands: { data: Array<{ id: number; name: string; category_id: number }> };
  categories: { data: Array<{ id: number; name: string }> };
}>();

// Transform brands and categories into Ant Design's options format
const brandOptions = computed(() => {
  return props.brands?.data?.map((brand: any) => ({
    value: brand.id,
    label: brand.name,
  })) || [];
});

const categoryOptions = computed(() => {
  return props.categories?.data?.map((category: any) => ({
    value: category.id,
    label: category.name,
  })) || [];
});

const filterOption = (input: string, option: any) => {
  return option.label.toLowerCase().includes(input.toLowerCase());
};

// Format date
const formatDate = (date: string) => {
  return date ? dayjs(date).format('DD-MM-YYYY hh:mm A') : 'N/A';
};

// DataTable columns for products
const dataTableColumns = [
  {
    title: translations.value.sr || 'Sr.',
    data: null,
    render: (data: any, type: any, row: any, meta: any) => meta.row + 1,
  },
  {
    title: translations.value.image || 'Image',
    data: 'thumnail_img',
    orderable: false,
    render: (data: string) =>
      data
        ? `<img src="/storage/${data}" class="w-12 h-12 object-cover rounded mb-1 cursor-pointer" style="display:inline-block;" />`
        : '<span class="text-gray-400 mb-1">No Image</span>',
  },
  { title: translations.value.name || 'Name', data: 'name' },
  { title: translations.value.stock || 'Stock', data: 'stock' },
  { title: translations.value.purchase_price || 'Purchase Price', data: 'purchase_price' },
  { title: translations.value.sale_price || 'Sale Price', data: 'sale_price' },
  {
    title: translations.value.discount || 'Discount',
    data: 'discount',
    render: (data: number) => (data ? `${data}%` : '0%'),
  },
  {
    title: translations.value.final_price || 'Final Price',
    data: 'final_price',
    render: (data: number, type: any, row: any) => data || row.sale_price,
  },
  { title: translations.value.category || 'Category', data: 'category_name' },
  { title: translations.value.brand || 'Brand', data: 'brand_name' },
  {
    title: translations.value.created_at || 'Created At',
    data: 'created_at',
    render: (data: string) => formatDate(data),
  },
  {
    title: translations.value.action || 'Action',
    data: null,
    orderable: false,
    render: (data: any, type: any, row: any) => `
      <button class="edit-btn p-2" data-id="${row.id}" title="${translations.value.edit || 'Edit'}">
        <i class="fa fa-pencil-square-o text-green-500"></i>
      </button>
      <button class="delete-btn p-2" data-id="${row.id}" title="${translations.value.delete || 'Delete'}">
        <i class="fa fa-trash text-red-500"></i>
      </button>
    `,
  },
];

// DataTable options
const options = {
  paging: true,
  searching: true,
  ordering: true,
  responsive: true,
  pageLength: 15,
  createdRow: (row: HTMLElement, data: any) => {
    setTimeout(() => {
      row.querySelector('.edit-btn')?.addEventListener('click', () => openEditModal(data));
      row.querySelector('.delete-btn')?.addEventListener('click', () => deleteProduct(data.id));
      row.querySelector('img')?.addEventListener('click', () => {
        if (data.thumnail_img) openImagePreview(data.thumnail_img);
      });
    }, 0);
  },
};

// Forms
const form = useForm({
  name: '',
  description: '',
  category_id: null,
  brand_id: null,
  thumnail_img: null as File | null,
  gallary_img: null as File | null,
  stock: 0,
  purchase_price: 0,
  sale_price: 0,
  discount: 0,
  final_price: 0,
  barcode: '',
  feature: false,
  status: 'active',
});

const editForm = useForm({
  id: null,
  name: '',
  description: '',
  category_id: null,
  brand_id: null,
  thumnail_img: null as File | null,
  gallary_img: null as File | null,
  stock: 0,
  purchase_price: 0,
  sale_price: 0,
  discount: 0,
  final_price: 0,
  barcode: '',
  feature: false,
  status: 'active',
  _method: 'PUT',
});

// Modal states
const isAddProductModalVisible = ref(false);
const isEditModalVisible = ref(false);
const isImagePreviewModalVisible = ref(false);
const previewImageUrl = ref('');
const imagePreview = ref<string | null>(null);
const editImagePreview = ref('');
const currentImage = ref('');

// Add product modal
const openAddProductModal = () => {
  form.reset();
  imagePreview.value = null;
  isAddProductModalVisible.value = true;
};

// Edit product modal
const openEditModal = (product: any) => {
  editForm.id = product.id;
  editForm.name = product.name;
  editForm.description = product.description;
  editForm.category_id = product.category_id;
  editForm.brand_id = product.brand_id;
  editForm.stock = product.stock;
  editForm.purchase_price = product.purchase_price;
  editForm.sale_price = product.sale_price;
  editForm.discount = product.discount;
  editForm.final_price = product.final_price;
  editForm.barcode = product.barcode;
  editForm.feature = product.feature;
  editForm.status = product.status;
  editForm.thumnail_img = null;
  currentImage.value = product.thumnail_img;
  editImagePreview.value = '';
  isEditModalVisible.value = true;
};

// Delete product
const deleteProduct = (id: number) => {
  Modal.confirm({
    title: translations.value.delete_product_confirm || 'Are you sure you want to delete this Product?',
    content: translations.value.delete_product_warning || 'This action cannot be undone.',
    okText: translations.value.confirm_delete_ok || 'Yes, Delete',
    okType: 'danger',
    cancelText: translations.value.confirm_delete_cancel || 'Cancel',
    onOk() {
      isLoading.value = true;
      useForm({}).delete(route('admin.product.delete', { id }), {
        onSuccess: () => {},
        onFinish: () => {
          isLoading.value = false;
        },
      });
    },
  });
};

// Save product
const saveProduct = () => {
  isLoading.value = true;
  form.post(route('admin.product.store'), {
    onSuccess: () => {
      form.reset();
      isAddProductModalVisible.value = false;
      if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
        imagePreview.value = null;
      }
    },
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

// Update product
const updateProduct = () => {
  isLoading.value = true;
  editForm.post(route('admin.product.update', { id: editForm.id }), {
    onSuccess: () => {
      isEditModalVisible.value = false;
      if (editImagePreview.value) {
        URL.revokeObjectURL(editImagePreview.value);
        editImagePreview.value = '';
      }
    },
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

// Image handling
const handleProductImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.thumnail_img = target.files[0];
    imagePreview.value = URL.createObjectURL(target.files[0]);
  }
};

const handleEditImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    editForm.thumnail_img = target.files[0];
    editImagePreview.value = URL.createObjectURL(target.files[0]);
  }
};

const openImagePreview = (imagePath: string) => {
  previewImageUrl.value = '/storage/' + imagePath;
  isImagePreviewModalVisible.value = true;
};

const getOriginalFilename = (path: string) => {
  if (!path) return '';
  const parts = path.split('/');
  return parts[parts.length - 1];
};
</script>

<template>
  <div v-if="isLoading" class="loading-overlay">
    <a-spin size="large" />
  </div>
  <AdminLayout>
    <Head :title="translations.product_list || 'Product List'" />
    <a-row>
      <a-col :xs="24">
        <div class="bg-white rounded-lg responsive-table p-4 shadow-md">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold mb-4">{{ translations.product_list || 'Product List' }}</h2>
            <div>
              <a-button @click="openAddProductModal" type="default">
                {{ translations.add_product || 'Add Product' }}
              </a-button>
              <Link :href="route('admin.product-log')">
                <a-button type="default">
                  {{ translations.product_logs || 'Product Logs' }}
                </a-button>
              </Link>
            </div>
          </div>
          <DataTable
            v-if="products?.data"
            :data="products.data"
            :columns="dataTableColumns"
            :options="options"
            class="display"
          >
            <thead>
              <tr>
                <th>{{ translations.sr || 'Sr.' }}</th>
                <th>{{ translations.image || 'Image' }}</th>
                <th>{{ translations.name || 'Name' }}</th>
                <th>{{ translations.stock || 'Stock' }}</th>
                <th>{{ translations.purchase_price || 'Purchase Price' }}</th>
                <th>{{ translations.sale_price || 'Sale Price' }}</th>
                <th>{{ translations.discount || 'Discount' }}</th>
                <th>{{ translations.final_price || 'Final Price' }}</th>
                <th>{{ translations.category || 'Category' }}</th>
                <th>{{ translations.brand || 'Brand' }}</th>
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

    <!-- Add Product Modal -->
    <a-modal
      v-model:open="isAddProductModalVisible"
      :title="translations.add_product || 'Add Product'"
      @cancel="isAddProductModalVisible = false"
      :footer="null"
    >
      <form @submit.prevent="saveProduct()" enctype="multipart/form-data">
        <div class="mb-4">
          <label class="block">{{ translations.category || 'Category' }}</label>
          <a-select
            v-model:value="form.category_id"
            show-search
            :placeholder="translations.select_category || 'Select a Category'"
            class="mt-2 w-full"
            :options="categoryOptions"
            :filter-option="filterOption"
          ></a-select>
          <div v-if="form.errors.category_id" class="text-red-500">{{ form.errors.category_id }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.brand || 'Brand' }}</label>
          <a-select
            v-model:value="form.brand_id"
            show-search
            :placeholder="translations.select_brand || 'Select a Brand'"
            class="mt-2 w-full"
            :options="brandOptions"
            :filter-option="filterOption"
          ></a-select>
          <div v-if="form.errors.brand_id" class="text-red-500">{{ form.errors.brand_id }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.name || 'Name' }}</label>
          <a-input
            v-model:value="form.name"
            class="mt-2 w-full"
            :placeholder="translations.name_placeholder || 'Enter Name'"
          />
          <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.description || 'Description' }}</label>
          <a-textarea
            v-model:value="form.description"
            class="mt-2 w-full"
            :placeholder="translations.eneter_description || 'Enter Description'"
            :auto-size="{ minRows: 2, maxRows: 5 }"
          />
          <div v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.stock || 'Stock' }}</label>
          <a-input-number
            v-model:value="form.stock"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_stock || 'Enter Stock'"
          />
          <div v-if="form.errors.stock" class="text-red-500">{{ form.errors.stock }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.purchase_price || 'Purchase Price' }}</label>
          <a-input-number
            v-model:value="form.purchase_price"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_purchase_price || 'Enter Purchase Price'"
          />
          <div v-if="form.errors.purchase_price" class="text-red-500">{{ form.errors.purchase_price }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.sale_price || 'Sale Price' }}</label>
          <a-input-number
            v-model:value="form.sale_price"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_sale_price || 'Enter Sale Price'"
          />
          <div v-if="form.errors.sale_price" class="text-red-500">{{ form.errors.sale_price }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.discount || 'Discount (%)' }}</label>
          <a-input-number
            v-model:value="form.discount"
            class="mt-2 w-full"
            :min="0"
            :max="100"
            :placeholder="0"
          />
          <div v-if="form.errors.discount" class="text-red-500">{{ form.errors.discount }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.final_price || 'Final Price' }}</label>
          <a-input-number
            v-model:value="form.final_price"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_final_price || 'Enter Product Price'"
          />
          <div v-if="form.errors.final_price" class="text-red-500">{{ form.errors.final_price }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.barcode || 'Barcode' }}</label>
          <a-input
            v-model:value="form.barcode"
            class="mt-2 w-full"
            :placeholder="translations.enter_barcode || 'Enter Barcode'"
          />
          <div v-if="form.errors.barcode" class="text-red-500">{{ form.errors.barcode }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.feature || 'Feature' }}</label>
          <a-checkbox v-model:checked="form.feature" class="mt-2">
            {{ translations.featured_product || 'Featured Product' }}
          </a-checkbox>
          <div v-if="form.errors.feature" class="text-red-500">{{ form.errors.feature }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.status || 'Status' }}</label>
          <a-select
            v-model:value="form.status"
            class="mt-2 w-full"
            :options="[
              { value: 'active', label: translations.value.active || 'Active' },
              { value: 'inactive', label: translations.value.inactive || 'Inactive' },
            ]"
          ></a-select>
          <div v-if="form.errors.status" class="text-red-500">{{ form.errors.status }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.image || 'Thumbnail Image' }}</label>
          <input
            type="file"
            @change="handleProductImageChange"
            accept="image/*"
            class="mt-2 w-full p-2 border rounded"
          />
          <div v-if="form.errors.thumnail_img" class="text-red-500">{{ form.errors.thumnail_img }}</div>
          <div v-if="imagePreview" class="mt-2">
            <p class="text-sm text-gray-600 mb-1">{{ translations.preview || 'Preview' }}</p>
            <img :src="imagePreview" alt="Image Preview" class="w-24 h-24 object-cover rounded border" />
          </div>
        </div>
        <div class="text-right">
          <a-button type="default" @click="isAddProductModalVisible = false">
            {{ translations.cancel || 'Cancel' }}
          </a-button>
          <a-button type="primary" html-type="submit" class="ml-2">
            {{ translations.save || 'Save' }}
          </a-button>
        </div>
      </form>
    </a-modal>

    <!-- Edit Product Modal -->
    <a-modal
      v-model:open="isEditModalVisible"
      :title="translations.update || 'Edit Product'"
      @cancel="isEditModalVisible = false"
      :footer="null"
    >
      <form @submit.prevent="updateProduct()" enctype="multipart/form-data">
        <div class="mb-4">
          <label class="block">{{ translations.category || 'Category' }}</label>
          <a-select
            v-model:value="editForm.category_id"
            show-search
            :placeholder="translations.select_category || 'Select a Category'"
            class="mt-2 w-full"
            :options="categoryOptions"
            :filter-option="filterOption"
          ></a-select>
          <div v-if="editForm.errors.category_id" class="text-red-500">{{ editForm.errors.category_id }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.brand || 'Brand' }}</label>
          <a-select
            v-model:value="editForm.brand_id"
            show-search
            :placeholder="translations.select_brand || 'Select a Brand'"
            class="mt-2 w-full"
            :options="brandOptions"
            :filter-option="filterOption"
          ></a-select>
          <div v-if="editForm.errors.brand_id" class="text-red-500">{{ editForm.errors.brand_id }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.name || 'Name' }}</label>
          <a-input
            v-model:value="editForm.name"
            class="mt-2 w-full"
            :placeholder="translations.name_placeholder || 'Enter Name'"
          />
          <div v-if="editForm.errors.name" class="text-red-500">{{ editForm.errors.name }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.description || 'Description' }}</label>
          <a-textarea
            v-model:value="editForm.description"
            class="mt-2 w-full"
            :placeholder="translations.eneter_description || 'Enter Description'"
            :auto-size="{ minRows: 2, maxRows: 5 }"
          />
          <div v-if="editForm.errors.description" class="text-red-500">{{ editForm.errors.description }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.stock || 'Stock' }}</label>
          <a-input-number
            v-model:value="editForm.stock"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_stock || 'Enter Stock'"
          />
          <div v-if="editForm.errors.stock" class="text-red-500">{{ editForm.errors.stock }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.purchase_price || 'Purchase Price' }}</label>
          <a-input-number
            v-model:value="editForm.purchase_price"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_purchase_price || 'Enter Purchase Price'"
          />
          <div v-if="editForm.errors.purchase_price" class="text-red-500">{{ editForm.errors.purchase_price }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.sale_price || 'Sale Price' }}</label>
          <a-input-number
            v-model:value="editForm.sale_price"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_sale_price || 'Enter Sale Price'"
          />
          <div v-if="editForm.errors.sale_price" class="text-red-500">{{ editForm.errors.sale_price }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.discount || 'Discount (%)' }}</label>
          <a-input-number
            v-model:value="editForm.discount"
            class="mt-2 w-full"
            :min="0"
            :max="100"
            :placeholder="0"
          />
          <div v-if="editForm.errors.discount" class="text-red-500">{{ editForm.errors.discount }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.final_price || 'Final Price' }}</label>
          <a-input-number
            v-model:value="editForm.final_price"
            class="mt-2 w-full"
            :min="0"
            :placeholder="translations.enter_final_price || 'Enter Final Price'"
          />
          <div v-if="editForm.errors.final_price" class="text-red-500">{{ editForm.errors.final_price }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.barcode || 'Barcode' }}</label>
          <a-input
            v-model:value="editForm.barcode"
            class="mt-2 w-full"
            :placeholder="translations.enter_barcode || 'Enter Barcode'"
          />
          <div v-if="editForm.errors.barcode" class="text-red-500">{{ editForm.errors.barcode }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.feature || 'Feature' }}</label>
          <a-checkbox v-model:checked="editForm.feature" class="mt-2">
            {{ translations.featured_product || 'Featured Product' }}
          </a-checkbox>
          <div v-if="editForm.errors.feature" class="text-red-500">{{ editForm.errors.feature }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.status || 'Status' }}</label>
          <a-select
            v-model:value="editForm.status"
            class="mt-2 w-full"
            :options="[
              { value: 'active', label: translations.value.active || 'Active' },
              { value: 'inactive', label: translations.value.inactive || 'Inactive' },
            ]"
          ></a-select>
          <div v-if="editForm.errors.status" class="text-red-500">{{ editForm.errors.status }}</div>
        </div>
        <div class="mb-4">
          <label class="block">{{ translations.image || 'Thumbnail Image' }}</label>
          <div v-if="currentImage" class="mb-2">
            <p class="text-sm text-gray-500 mb-1">{{ translations.current_image || 'Current Image' }}</p>
            <img
              :src="'/storage/' + currentImage"
              alt="Current Product Image"
              class="w-24 h-24 object-cover rounded border"
            />
            <p class="text-xs text-gray-500 mt-1">{{ getOriginalFilename(currentImage) }}</p>
          </div>
          <input
            type="file"
            @change="handleEditImageChange"
            accept="image/*"
            class="mt-2 w-full p-2 border rounded"
          />
          <div v-if="editForm.errors.thumnail_img" class="text-red-500">{{ editForm.errors.thumnail_img }}</div>
          <div class="mt-2 text-sm text-gray-500">
            {{ translations.keep_current_image || 'Leave empty to keep the current image' }}
          </div>
          <div v-if="editImagePreview" class="mt-2">
            <p class="text-sm text-gray-600 mb-1">{{ translations.new_image_preview || 'New Image Preview' }}</p>
            <img :src="editImagePreview" alt="New Image Preview" class="w-24 h-24 object-cover rounded border" />
          </div>
        </div>
        <div class="text-right">
          <a-button type="default" @click="isEditModalVisible = false">
            {{ translations.cancel || 'Cancel' }}
          </a-button>
          <a-button type="primary" html-type="submit" class="ml-2">
            {{ translations.update || 'Update' }}
          </a-button>
        </div>
      </form>
    </a-modal>

    <!-- Image Preview Modal -->
    <a-modal
      v-model:open="isImagePreviewModalVisible"
      :title="translations.preview || 'Image Preview'"
      @cancel="isImagePreviewModalVisible = false"
      :footer="null"
      width="600px"
    >
      <div class="flex justify-center p-4">
        <img :src="previewImageUrl" alt="Full Size Image" class="max-w-full max-h-[500px] object-cover" />
      </div>
    </a-modal>
  </AdminLayout>
</template>

<style scoped>
.display {
  width: 100%;
}
</style>
