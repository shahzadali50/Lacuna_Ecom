<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Pagination } from 'ant-design-vue';

interface PaginationData {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
    from: number;
    to: number;
}

interface Props {
    paginationData: any; // Contains current_page, total, per_page, etc.
    routeName: string; // Route name for pagination (e.g., 'all.products')
}

const props = defineProps<Props>();

// Compute pagination data from props
const pagination = computed<PaginationData>(() => ({
    current_page: props.paginationData?.current_page || 1,
    last_page: props.paginationData?.last_page || 1,
    total: props.paginationData?.total || 0,
    per_page: props.paginationData?.per_page || 5,
    from: props.paginationData?.from || 1,
    to: props.paginationData?.to || 0,
}));

// Handle page change
const handlePageChange = (newPage: number) => {
    console.log('Navigating to page:', newPage); // Debug log
    router.visit(route(props.routeName, { page: newPage }), {
        preserveState: false, // Ensure full prop refresh
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="text-center mt-8 sm:mt-12">
        <Pagination :current="pagination.current_page" :total="pagination.total" :page-size="pagination.per_page"
            :show-total="(total, range) => `${range[0]}-${range[1]} of ${total} items`" @change="handlePageChange"
            class="inline-block" />
    </div>
</template>

<style scoped>
/* Add any pagination-specific styles here if needed */
</style>
