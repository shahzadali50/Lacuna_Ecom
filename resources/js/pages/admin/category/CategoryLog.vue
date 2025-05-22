<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import dayjs from "dayjs";
import { Link } from '@inertiajs/vue3';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';


DataTable.use(DataTablesCore);
const data = [
  [1, 2],
  [3, 4],
  [3, 4],
  [3, 4],
  [3, 4],
  [3, 4],
];
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

const columns = [
    { title: 'ID', dataIndex: 'id', key: 'id' },
    { title: 'Note', dataIndex: 'note', key: 'note' },
    { title: 'Category', dataIndex: 'category_name', key: 'category' }, // ✅ Fixed key
    { title: 'User', dataIndex: 'user_name', key: 'user' }, // ✅ Fixed key
    { title: 'Created At', dataIndex: 'created_at', key: 'created_at' },
];

defineProps({
    CategoryLog: Object, // ✅ Ensure correct prop name
});
</script>

<template>
    <AdminLayout>
    <Head title="Category Logs" />
        <a-row>
        <a-col-12>
          <DataTable :data="data" class="display">
        <thead>
            <tr>
                <th>A</th>
                <th>B</th>
            </tr>
        </thead>
    </DataTable>



        </a-col-12>
            <a-col :span="24">
                <div class="bg-white rounded-lg responsive-table p-4 shadow-md">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Category Logs</h2>
                        <Link :href="route('admin.categories')" >
                            <a-button type="default">Back</a-button>
                        </Link>
                    </div>

                    <a-table  v-if="CategoryLog" :columns="columns" :data-source="CategoryLog.data" rowKey="id" id="datatable">
                        <template #bodyCell="{ column, record,index  }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-else-if="column.dataIndex === 'note'">
                                {{ record.note }}
                            </template>
                            <template v-else-if="column.dataIndex === 'category_name'">
                                {{ record.category_name ?? 'Deleted Category' }}
                            </template>
                            <template v-else-if="column.dataIndex === 'user_name'">
                                {{ record.user?.name ?? 'N/A' }}
                            </template>
                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                        </template>
                    </a-table>

                </div>
            </a-col>
        </a-row>
    </AdminLayout>
</template>
