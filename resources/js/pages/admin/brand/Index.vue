<script setup lang="ts">
import AdminLayout from "@/layouts/AdminLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { Modal } from "ant-design-vue";
import dayjs from "dayjs";
const isLoading = ref(false);
import { ref, computed } from "vue";

const page = usePage();

const translations = computed(() => {
    return page.props.translations?.dashboard_all_pages || {};
});

const columns = computed(() => [
    { title: translations.value.sr || 'Sr.', dataIndex: 'id', key: 'id' },
    { title: translations.value.image || 'Image', dataIndex: 'image', key: 'image' },
    { title: translations.value.name || 'Name', dataIndex: 'name', key: 'name' },
    { title: translations.value.description || 'Description', dataIndex: 'description', key: 'description' },
    { title: translations.value.category || 'Category', dataIndex: 'category', key: 'category' },
    { title: translations.value.created_at || 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: translations.value.action || 'Action', dataIndex: 'action', key: 'action' },
]);
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

// Define props correctly

const props = defineProps({
    categories: Array,
    brands: Object,
});
// Transform categories into Ant Design's options format

const categoryOptions = computed(() => {
    return props.categories?.map((category: any) => ({
        value: category.id,
        label: category.name
    })) || [];
});

const filterOption = (input: string, option: any) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const form = useForm({
    name: "",
    description: "",
    category_id: null,
    image: null as File | null,
});
const editForm = useForm({
    id: null,
    name: "",
    description: "",
    image: null as File | null,
    _method: 'PUT'
});
const deleteBrand = (id: number) => {
    Modal.confirm({
        title: translations.value.confirm_delete_title || 'Are you sure you want to delete',
        content: translations.value.delete_brand_warning || 'This action cannot be undone.',
        okText: translations.value.confirm_delete_ok || 'Yes, Delete',
        okType: "danger",
        cancelText: translations.value.confirm_delete_cancel || 'Cancel',
        onOk() {
            isLoading.value = true;
            form.delete(route("admin.brand.delete", { id: id }), {
                onSuccess: () => {

                },
                onFinish: () => {
                    isLoading.value = false;
                },
            });
        },
    });
};
const isEditModalVisible = ref(false);
const isAddBrandModalVisible = ref(false);
const previewImageUrl = ref('');

const isImagePreviewModalVisible = ref(false);
const openAddBrandModal = () => {
    form.reset();
    imagePreview.value = null;
    isAddBrandModalVisible.value = true;

};

const openEditModal = (brands: any) => {
    editForm.id = brands.id;
    editForm.name = brands.name;
    editForm.description = brands.description;
    editForm.image = null; // Reset image when opening modal
    currentImage.value = brands.image; // Store the current image path
    editImagePreview.value = ''; // Reset preview
    isEditModalVisible.value = true;

};
const imagePreview = ref(null);
const handleBrandImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.image = target.files[0];
        // Create preview URL
        imagePreview.value = URL.createObjectURL(target.files[0]);
    }
};
const saveBrand = () => {
    isLoading.value = true;
    form.post(route("admin.brand.store"), {
        onSuccess: () => {
            form.reset();
            isAddBrandModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
// Update brand
const updateBrand = () => {
    isLoading.value = true;
    editForm.post(route("admin.brand.update", { id: editForm.id }), {
        onSuccess: () => {
            isEditModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};


const openImagePreview = (imagePath: string) => {
    previewImageUrl.value = '/storage/' + imagePath;
    isImagePreviewModalVisible.value = true;
};

const editImagePreview = ref('');
const currentImage = ref('');

const handleEditImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        editForm.image = target.files[0];
        // Create preview URL
        editImagePreview.value = URL.createObjectURL(target.files[0]);
    }
};
</script>
<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>

        <Head :title="translations.brand_title || 'Brand List'" />
        <a-row>
            <a-col :xs="24">
                <div class="bg-white p-4  rounded-lg responsive-table">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold mb-4"> {{ translations.brand_title || 'Brand List' }}</h2>
                        <div>
                            <a-button @click="openAddBrandModal()" type="default"> {{ translations.add_brand || 'Add Brand' }}</a-button>
                            <Link :href="route('admin.brand-log')">
                            <a-button type="default"> {{ translations.brand_logs || 'Brand Logs' }}</a-button>
                            </Link>
                        </div>
                    </div>
                    <a-table :columns="columns" :data-source="brands?.data" rowKey="id">
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-if="column.dataIndex === 'image'">
                                <div>
                                    <img v-if="record.image" :src="'/storage/' + record.image" alt="Category Image"
                                        class="w-12 h-12 object-cover rounded mb-1 cursor-pointer hover:opacity-80 transition-opacity"
                                        @click="openImagePreview(record.image)" />
                                    <span v-else class="text-gray-400 mb-1">No Image</span>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-else-if="column.dataIndex === 'description'">
                                {{ record.description ? record.description : "N/A" }}
                            </template>
                            <template v-else-if="column.dataIndex === 'category'">
                                {{ record.category_name || "N/A" }}
                            </template>
                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.delete || 'Delete' }}</template>
                                    <a-button type="link" @click="deleteBrand(record.id)"><i
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

        <!-- Add Brand Modal -->
        <a-modal v-model:open="isAddBrandModalVisible" :title="translations.add_brand || 'Add Brand'" @cancel="isAddBrandModalVisible = false"
            :footer="null">
            <form @submit.prevent="saveBrand()" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block">{{ translations.category || 'Category' }} </label>
                    <a-select v-model:value="form.category_id" show-search placeholder="Select a Category"
                        class="mt-2 w-full" :options="categoryOptions" :filter-option="filterOption"></a-select>
                    <div v-if="form.errors.category_id" class="text-red-500">
                        {{ form.errors.category_id }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.name || 'Name' }}</label>
                    <a-input v-model:value="form.name" class="mt-2 w-full" placeholder="Enter Name" />
                    <div v-if="form.errors.name" class="text-red-500">
                        {{ form.errors.name }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.description || 'Description' }}</label>
                    <a-textarea v-model:value="form.description" class="mt-2 w-full" placeholder="Description"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="form.errors.description" class="text-red-500">
                        {{ form.errors.description }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.image || 'Image' }}</label>
                    <input type="file" @change="handleBrandImageChange" accept="image/*"
                        class="mt-2 w-full p-2 border rounded" />
                    <div v-if="form.errors.image" class="text-red-500">{{ form.errors.image }}</div>
                    <!-- Image Preview -->
                    <div v-if="imagePreview" class="mt-2">
                        <p class="text-sm text-gray-600 mb-1">Preview</p>
                        <img :src="imagePreview" alt="Image Preview" class="w-24 h-24 object-cover rounded border" />
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isAddBrandModalVisible = false">{{ translations.cancel || 'Cancel' }}</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">{{ translations.save || 'Save' }}</a-button>
                </div>
            </form>

        </a-modal>

        <!-- Edit Brand Modal -->
        <a-modal v-model:open="isEditModalVisible" :title="translations.update || 'Edit Brand'" @cancel="isEditModalVisible = false"
            :footer="null">
            <form @submit.prevent="updateBrand()" enctype="multipart/form-data">

                <div class="mb-4">
                    <label class="block">{{ translations.name || 'Name' }}</label>
                    <a-input v-model:value="editForm.name" class="mt-2 w-full" :placeholder="translations.name_placeholder || 'Enter Name'" />
                    <div v-if="editForm.errors.name" class="text-red-500">
                        {{ editForm.errors.name }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.description || 'Description' }}</label>
                    <a-textarea v-model:value="editForm.description" class="mt-2 w-full" :placeholder="translations.eneter_description || 'Enter Description'"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="editForm.errors.description" class="text-red-500">
                        {{ editForm.errors.description }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.image || 'Image' }}</label>
                    <div v-if="currentImage" class="mb-2">
                        <p class="text-sm text-gray-500 mb-1">{{ translations.current_image || 'Current Image' }}</p>
                        <img :src="'/storage/' + currentImage" alt="Current Brand Image"
                            class="w-24 h-24 object-cover rounded border" />
                    </div>
                    <input type="file" @change="handleEditImageChange" accept="image/*"
                        class="mt-2 w-full p-2 border rounded" />
                    <div v-if="editForm.errors.image" class="text-red-500">{{ editForm.errors.image }}</div>
                    <div class="mt-2 text-sm text-gray-500">
                        {{ translations.keep_current_image || 'Leave empty to keep the current image' }}
                    </div>
                    <!-- New Image Preview -->
                    <div v-if="editImagePreview" class="mt-2">
                        <p class="text-sm text-gray-600 mb-1">{{ translations.new_image_preview || 'New Image Preview' }}:</p>
                        <img :src="editImagePreview" alt="New Image Preview"
                            class="w-24 h-24 object-cover rounded border" />
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isEditModalVisible = false">{{ translations.cancel || 'Cancel' }}</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">{{ translations.update || 'Update' }}</a-button>
                </div>
            </form>
        </a-modal>
        <a-modal v-model:open="isImagePreviewModalVisible"   :title="translations.preview || 'Image Preview'"
            @cancel="isImagePreviewModalVisible = false" :footer="null" width="600px">
            <div class="flex justify-center p-4">
                <img :src="previewImageUrl" alt="Full Size Image" class="max-w-full max-h-[500px] object-cover" />
            </div>
        </a-modal>
    </AdminLayout>
</template>
