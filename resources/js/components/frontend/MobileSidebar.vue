<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
defineProps<{
    visible: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:visible', value: boolean): void;
}>();

const closeDrawer = () => {
    emit('update:visible', false);
};
</script>

<template>
    <a-drawer :visible="visible" placement="left" :closable="true" @close="closeDrawer" width="280">
        <div class="flex flex-col h-full">
            <!-- Navigation Links -->
            <nav class="flex-1">
                <a-menu mode="vertical">
                    <a-menu-item key="home">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                            Home
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="products">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                            Products
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="categories">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                            Categories
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="brands">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                            Brands
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="about">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                            About
                        </Link>
                    </a-menu-item>
                </a-menu>
            </nav>
        </div>
    </a-drawer>
</template>

<style scoped>

</style>
