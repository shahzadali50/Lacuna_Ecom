<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import UserLayout from '@/layouts/UserLayout.vue';
defineProps<{
    status?: string;
}>();

const { props } = usePage();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
        <UserLayout>
    <AuthLayout :title="props.translations.verify_email" :description="props.translations.verify_email_description">
        <Head :title="props.translations.email_verification" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ props.translations.verification_link_sent }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ props.translations.resend_verification }}
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> {{ props.translations.logout }} </TextLink>
        </form>
    </AuthLayout>
    </UserLayout>
</template>
