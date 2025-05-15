<script setup lang="ts">
import { ref, watch, computed, defineProps } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    SettingOutlined,
    DatabaseOutlined,
    AppstoreOutlined,
    TrademarkCircleOutlined,
    ShoppingOutlined,
    ShoppingCartOutlined,
    FileTextOutlined,
    OrderedListOutlined
} from '@ant-design/icons-vue';

// Define the type for translations
interface Translations {
    sidebar?: {
        dashboard?: string;
        categories?: string;
        brands?: string;
        products?: string;
        purchase_products?: string;
        get_orders?: string;
        order_list?: string;
        settings?: string;
    };
}

defineProps<{ collapsed: boolean }>();

const selectedKeys = ref<string[]>([]);
const page = usePage();
const currentPath = computed(() => page.url);

// Get translations from page props with proper typing
const translations = computed(() => {
    const props = page.props as { translations?: Translations };
    return props.translations?.sidebar || {};
});

// Watch URL changes to update active keys
watch(currentPath, (newUrl) => {
    selectedKeys.value = [newUrl];
}, { immediate: true });
</script>

<template>
    <a-layout-sider :collapsed="collapsed" :trigger="null" collapsible>
        <div class="logo">
            <h3 class="mb-0" v-if="!collapsed">LaCuna-Marketplace</h3>
        </div>
        <div class="scrollbar" id="style-2">
        <a-menu v-model:selectedKeys="selectedKeys" class="force-overflow">
            <!-- Dashboard -->
            <a-menu-item key="1" :class="{ 'active': currentPath.startsWith(route('admin.dashboard', {}, false)) }">
                <Link :href="route('admin.dashboard')">
                <DatabaseOutlined />
                <span>{{ translations.dashboard || 'Dashboard' }}</span>
                </Link>
            </a-menu-item>

            <!-- Categories -->
            <a-menu-item key="2"  :class="{ 'active': currentPath.startsWith(route('admin.categories', {}, false)) }">
                <Link :href="route('admin.categories')">
                <AppstoreOutlined  />
                <span>{{ translations.categories || 'Categories' }}</span>
                </Link>
            </a-menu-item>

            <!-- Brands -->
            <a-menu-item key="3" :class="{ 'active': currentPath.startsWith(route('admin.brands', {}, false)) }">
                <Link :href="route('admin.brands')">
                <TrademarkCircleOutlined  />
                <span>{{ translations.brands || 'Brands' }}</span>
                </Link>
            </a-menu-item>

            <!-- Products -->
            <a-menu-item key="4" :class="{ 'active': currentPath.startsWith(route('admin.products', {}, false)) }">
                <Link :href="route('admin.products')">
                <ShoppingOutlined />
                <span>{{ translations.products || 'Products' }}</span>
                </Link>
            </a-menu-item>

            <!-- Purchase Products -->
            <a-menu-item key="5" :class="{ 'active': currentPath.startsWith(route('admin.purchase.product.list', {}, false)) }">
                <Link :href="route('admin.purchase.product.list')">
                <ShoppingCartOutlined  />
                <span>{{ translations.purchase_products || 'Purchase Products' }}</span>
                </Link>
            </a-menu-item>

            <!-- Get Orders -->
            <!-- <a-menu-item key="6" :class="{ 'active': currentPath.startsWith(route('admin.order.create', {}, false)) }">
                <Link :href="route('admin.order.create')">
                <FileTextOutlined  />
                <span>{{ translations.get_orders || 'Get Orders' }}</span>
                </Link>
            </a-menu-item> -->

            <!-- Order List -->
            <a-menu-item key="7" :class="{ 'active': currentPath.startsWith(route('admin.order.list', {}, false)) }">
                <Link :href="route('admin.order.list')">
                <OrderedListOutlined  />
                <span>{{ translations.order_list || 'Order List' }}</span>
                </Link>
            </a-menu-item>

            <!-- Settings -->
            <a-menu-item key="8" :class="{ 'active': currentPath.startsWith(route('profile.edit', {}, false)) }">
                <Link :href="route('profile.edit')">
                <SettingOutlined />
                <span>{{ translations.settings || 'Settings' }}</span>
                </Link>
            </a-menu-item>
        </a-menu>
        </div>
    </a-layout-sider>
</template>

<style scoped>
.logo {
    margin-top: 10px;
    height: 42px;
    color: rgb(255, 255, 255) !important;
    background-color: #232330 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>
