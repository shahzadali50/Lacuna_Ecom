<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import UserLayout from '@/layouts/UserLayout.vue';
defineProps<{
    status?: string;
}>();

const { props } = usePage();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>

    <UserLayout>
    <AuthLayout :title="props.translations.forgot_password" :description="props.translations.enter_email_reset">
        <Head :title="props.translations.forgot_password" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">{{ props.translations.email }}</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus :placeholder="props.translations.email_placeholder" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        {{ props.translations.email_reset_link }}
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>{{ props.translations.or_return_to }}</span>
                <TextLink :href="route('login')">{{ props.translations.login }}</TextLink>
            </div>
        </div>
    </AuthLayout>
    </UserLayout>
</template>
