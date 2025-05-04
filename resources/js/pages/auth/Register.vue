<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import UserLayout from '@/layouts/UserLayout.vue';

const { props } = usePage();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <UserLayout>
    <AuthBase :title="props.translations.create_account" :description="props.translations.enter_details">
        <Head :title="props.translations.register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">{{ props.translations.name }}</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" :placeholder="props.translations.name_placeholder" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">{{ props.translations.email }}</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" :placeholder="props.translations.email_placeholder" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ props.translations.password }}</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        :placeholder="props.translations.password_placeholder"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">{{ props.translations.confirm_password }}</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        :placeholder="props.translations.confirm_password_placeholder"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ props.translations.create_account }}
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                {{ props.translations.have_account }}
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">{{ props.translations.login }}</TextLink>
            </div>
        </form>
    </AuthBase>
    </UserLayout>
</template>
