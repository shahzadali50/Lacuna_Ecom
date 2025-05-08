
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    HomeOutlined,
    AppstoreOutlined,
    HeartOutlined,
    ShoppingCartOutlined,
    UserOutlined
} from '@ant-design/icons-vue';

const props = defineProps({
    isAuthenticated: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['toggleCart', 'logout']);

const toggleCart = () => {
    emit('toggleCart');
};

const handleLogout = () => {
    emit('logout');
};
</script>
<template>
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="flex justify-around items-center h-16">
            <Link :href="route('home')" class="flex flex-col items-center">
                <HomeOutlined class="text-xl" />
                <span class="text-xs mt-1">Home</span>
            </Link>
            <Link :href="route('home')" class="flex flex-col items-center">
                <AppstoreOutlined class="text-xl" />
                <span class="text-xs mt-1">Categories</span>
            </Link>
            <Link :href="route('home')" class="flex flex-col items-center">
                <HeartOutlined class="text-xl" />
                <span class="text-xs mt-1">Favorites</span>
            </Link>
            <a class="flex flex-col items-center" @click="toggleCart">
                <ShoppingCartOutlined class="text-xl" />
                <span class="text-xs mt-1">Cart</span>
            </a>
            <a-dropdown placement="topLeft" trigger="click">
                <a class="flex flex-col items-center cursor-pointer">
                    <UserOutlined class="text-xl" />
                    <span class="text-xs mt-1">Account</span>
                </a>
                <template #overlay>
                    <a-menu>
                        <template v-if="!isAuthenticated">
                            <a-menu-item key="login">
                                <Link :href="route('login')" class="flex items-center">
                                    Login
                                </Link>
                            </a-menu-item>
                            <a-menu-item key="register">
                                <Link :href="route('register')" class="flex items-center">
                                    Register
                                </Link>
                            </a-menu-item>
                        </template>
                        <template v-else>
                            <a-menu-item key="profile">
                                <Link :href="route('dashboard')" class="flex items-center">
                                    Profile
                                </Link>
                            </a-menu-item>
                            <a-menu-divider />
                            <a-menu-item key="logout">
                                <a href="#" @click.prevent="handleLogout" class="flex items-center">
                                    Logout
                                </a>
                            </a-menu-item>
                        </template>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>
    </div>
</template>

