<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import dayjs from "dayjs";

const isLoading = ref(false);
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

const page = usePage();

const translations = computed(() => {
    return page.props.translations?.dashboard_all_pages || {};
});

const columns = computed(() => [
    { title: translations.value.sr || 'Sr.', dataIndex: 'id', key: 'id' },
    { title: translations.value.image || 'Image', dataIndex: 'image', key: 'image' },
    { title: translations.value.name || 'Name', dataIndex: 'name', key: 'name' },
    { title: translations.value.description || 'Description', dataIndex: 'description', key: 'description' },
    { title: translations.value.created_at || 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: translations.value.action || 'Action', dataIndex: 'action', key: 'action' },
]);

defineProps({
    categories: Object,
})

const form = useForm({
    name: '',
    description: '',
    id: null,
    image: null as File | null
})
const editForm = useForm({
    id: null,
    name: '',
    description: '',
    image: null as File | null,
    _method: 'PUT'
});
const brandForm = useForm({
    id: null,
    name: '',
    description: '',
    category_id: null,
    image: null as File | null,
});

const isAddCategoryModalVisible = ref(false);
const selectedCategoryName = ref('');
const currentImage = ref('');
const imagePreview = ref('');
const editImagePreview = ref('');
const isImagePreviewModalVisible = ref(false);
const previewImageUrl = ref('');

const openAddCategoryModal = () => {
    form.reset();
    imagePreview.value = '';
    isAddCategoryModalVisible.value = true;
};

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.image = target.files[0];
        // Create preview URL
        imagePreview.value = URL.createObjectURL(target.files[0]);
    }
};

const handleBrandImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        brandForm.image = target.files[0];
        // Create preview URL
        imagePreview.value = URL.createObjectURL(target.files[0]);
    }
};

const handleEditImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        editForm.image = target.files[0];
        // Create preview URL
        editImagePreview.value = URL.createObjectURL(target.files[0]);
    }
};

const saveCategory = () => {
    isLoading.value = true;
    form.post(route('admin.category.store'), {
        onSuccess: () => {
            form.reset();
            isAddCategoryModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false; // ✅ Stop loading
        }
    })
}
const deleteCategory = (id: number) => {
    Modal.confirm({
        title: translations.value.confirm_delete_title || 'Are you sure you want to delete',
        content: translations.value.confirm_delete_content_category || 'Deleting this category will also remove all associated brands. This action is irreversible. Are you sure you want to proceed?',
        okText: translations.value.confirm_delete_ok || 'Yes, Delete',
        okType: 'danger',
        cancelText: translations.value.confirm_delete_cancel || 'Cancel',
        onOk() {
            isLoading.value = true;
            form.delete(route('admin.category.delete', { id: id }), {
                onFinish: () => {
                    isLoading.value = false; // ✅ Stop loading
                }
            });
        },
    });
};

const isEditModalVisible = ref(false);
const isbrandModalVisible = ref(false);

const openEditModal = (category: any) => {
    editForm.id = category.id;
    editForm.name = category.name;
    editForm.description = category.description;
    editForm.image = null; // Reset image when opening modal
    currentImage.value = category.image; // Store the current image path
    editImagePreview.value = ''; // Reset preview
    isEditModalVisible.value = true;
};
const openBrandModal = (record: any) => {
    selectedCategoryName.value = record.name;
    brandForm.category_id = record.id;
    isbrandModalVisible.value = true;
};
const saveBrand = () => {
    isLoading.value = true;
    brandForm.post(route('admin.brand.store'), {
        onSuccess: () => {
            brandForm.reset();
            isbrandModalVisible.value = false;
            if (imagePreview.value) {
                URL.revokeObjectURL(imagePreview.value); // Clean up the object URL
                imagePreview.value = '';
            }
        },
        onFinish: () => {
            isLoading.value = false;
        }
    })
}

// Update category
const updateCategory = () => {
    isLoading.value = true;
    editForm.post(route('admin.category.update', { id: editForm.id }), {
        onSuccess: () => {
            isEditModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

// Function to get original filename from storage path
const getOriginalFilename = (path: string) => {
    if (!path) return '';
    // Extract the filename from the path
    const parts = path.split('/');
    const filename = parts[parts.length - 1];
    return filename;
};

const openImagePreview = (imagePath: string) => {
    previewImageUrl.value = '/storage/' + imagePath;
    isImagePreviewModalVisible.value = true;
};
</script>

<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>
        <Head :title="translations.category_title || 'Category List'" />

        <a-row>
            <a-col :xs="24">
                <div class="bg-white rounded-lg responsive-table p-4 shadow-md w-full">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold mb-4">{{ translations.category_title || 'Category List' }}</h2>
                        <div>
                            <a-button @click="openAddCategoryModal()" type="default">
                                {{ translations.add_category || 'Add Category' }}
                            </a-button>
                            <Link :href="route('admin.category.log')">
                                <a-button type="default">
                                    {{ translations.category_logs || 'Category Logs' }}
                                </a-button>
                            </Link>
                        </div>
                    </div>
                    <a-table :columns="columns" :data-source="categories?.data" rowKey="id" :scroll="{ x: 200 }">
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-if="column.dataIndex === 'image'">
                                <div >
                                    <img
                                        v-if="record.image"
                                        :src="'/storage/' + record.image"
                                        alt="Category Image"
                                        class="w-12 h-12 object-cover rounded mb-1 cursor-pointer hover:opacity-80 transition-opacity"
                                        @click="openImagePreview(record.image)"
                                    />
                                    <span v-else class="text-gray-400 mb-1">{{ translations.no_image || 'No Image' }}</span>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'description'">
                                {{ record.description ?? 'N/A' }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.edit || 'Edit' }}</template>
                                    <a-button type="link" @click="openEditModal(record)"><i
                                            class="fa fa-pencil-square-o text-s text-green-500"
                                            aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.delete || 'Delete' }}</template>
                                    <a-button type="link" @click="deleteCategory(record.id)"><i
                                            class="fa fa-trash text-red-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.add_brand || 'Add Brand' }}</template>
                                    <a-button type="link" @click="openBrandModal(record)"><i
                                            class="fa fa-creative-commons" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>{{ translations.brand_list || 'Brand List' }}</template>
                                    <Link :href="route('admin.related-brand-list', record.slug)"
                                        class="text-blue-500 hover:underline"><i class="fa fa-list text-slate-800"
                                        aria-hidden="true"></i></Link>
                                </a-tooltip>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>

        <!-- Add Category Modal -->
        <a-modal v-model:open="isAddCategoryModalVisible" :title="translations.add_category || 'Add Category'"
            @cancel="isAddCategoryModalVisible = false" :footer="null">
            <form @submit.prevent="saveCategory()" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block">{{ translations.name || 'Name' }}</label>
                    <a-input v-model:value="form.name" class="mt-2 w-full" :placeholder="translations.name_placeholder || 'Enter Name'" />
                    <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.description || 'Description' }}</label>
                    <a-textarea v-model:value="form.description" class="mt-2 w-full" :placeholder="translations.eneter_description || 'Enter Description'"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.image || 'Image' }}</label>
                    <input type="file" @change="handleImageChange" accept="image/*"
                        class="mt-2 w-full p-2 border rounded" />
                    <div v-if="form.errors.image" class="text-red-500">{{ form.errors.image }}</div>
                    <!-- Image Preview -->
                    <div v-if="imagePreview" class="mt-2">
                        <p class="text-sm text-gray-600 mb-1">{{ translations.preview || 'Preview' }}:</p>
                        <img :src="imagePreview" alt="Image Preview" class="w-24 h-24 object-cover rounded border" />
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isAddCategoryModalVisible = false">{{ translations.cancel || 'Cancel' }}</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">{{ translations.add || 'Add' }}</a-button>
                </div>
            </form>
        </a-modal>

        <!-- Edit Category Modal -->
        <a-modal v-model:open="isEditModalVisible" :title="translations.update || 'Edit Category'" @cancel="isEditModalVisible = false"
            :footer="null">
            <form @submit.prevent="updateCategory()" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block">{{ translations.name || 'Name' }}</label>
                    <a-input v-model:value="editForm.name" class="mt-2 w-full" :placeholder="translations.name_placeholder || 'Enter Name'" />
                    <div v-if="editForm.errors.name" class="text-red-500">{{ editForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.description || 'Description' }}</label>
                    <a-textarea v-model:value="editForm.description" class="mt-2 w-full" :placeholder="translations.eneter_description || 'Enter Description'"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="editForm.errors.description" class="text-red-500">{{ editForm.errors.description }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.image || 'Image' }}</label>
                    <div v-if="currentImage" class="mb-2">
                        <p class="text-sm text-gray-500 mb-1">{{ translations.current_image || 'Current Image' }}</p>
                        <img :src="'/storage/' + currentImage" alt="Current Category Image"
                            class="w-24 h-24 object-cover rounded border" />
                        <p class="text-xs text-gray-500 mt-1">{{ getOriginalFilename(currentImage) }}</p>
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

        <!-- Brand Modal -->
        <a-modal v-model:open="isbrandModalVisible" :title="translations.add_brand || 'Add Brand'" @cancel="isbrandModalVisible = false"
            :footer="null">
            <h4 class="text-md">{{ translations.category || 'Category' }} ({{ selectedCategoryName }})</h4>
            <form @submit.prevent="saveBrand()" enctype="multipart/form-data">
                <a-input hidden v-model:value="brandForm.category_id" class="mt-2 w-full" :placeholder="translations.name_placeholder || 'Enter Name'" />
                <div class="mb-4">
                    <label class="block">{{ translations.name || 'Name' }}</label>
                    <a-input v-model:value="brandForm.name" class="mt-2 w-full" :placeholder="translations.name_placeholder || 'Enter Name'" />
                    <div v-if="brandForm.errors.name" class="text-red-500">{{ brandForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.description || 'Description' }}</label>
                    <a-textarea v-model:value="brandForm.description" class="mt-2 w-full" :placeholder="translations.eneter_description || 'Enter Description'"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="brandForm.errors.description" class="text-red-500">{{ brandForm.errors.description }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">{{ translations.image || 'Image' }}</label>
                    <input type="file" @change="handleBrandImageChange" accept="image/*"
                        class="mt-2 w-full p-2 border rounded" />
                    <div v-if="brandForm.errors.image" class="text-red-500">{{ brandForm.errors.image }}</div>
                    <!-- Image Preview -->
                    <div v-if="imagePreview" class="mt-2">
                        <p class="text-sm text-gray-600 mb-1">{{ translations.preview || 'Preview' }}:</p>
                        <img :src="imagePreview" alt="Image Preview" class="w-24 h-24 object-cover rounded border" />
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isbrandModalVisible = false">{{ translations.cancel || 'Cancel' }}</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">{{ translations.save || 'Save' }}</a-button>
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
                <img
                    :src="previewImageUrl"
                    alt="Full Size Image"
                    class="max-w-full max-h-[500px] object-cover"
                />
            </div>
        </a-modal>
    </AdminLayout>
</template>
<style scoped></style>
