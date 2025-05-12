<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import TextLink from "@/components/TextLink.vue";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { LoaderCircle } from "lucide-vue-next";
import { message } from "ant-design-vue";
const props = defineProps<{
    open: boolean,
    translations: any,
    canResetPassword: boolean;
}>();
const emit = defineEmits<{
    (e: 'update:open', value: boolean): void
}>();

const page = usePage();
// Directly assign translations object
const translations = page.props.translations || {};

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
      
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <a-modal v-model:open="props.open" :title="translations.login" @cancel="emit('update:open', false)" :footer="null">
        <form @submit.prevent="submit">

            <div class="mb-4">
                <label class="block">{{ translations.email }}</label>
                <a-input v-model:value="form.email" class="mt-2 w-full"
                    :placeholder="translations.email_placeholder || 'Enter Your Email'" />
                <InputError :message="form.errors.email" />
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <Label for="password">{{ translations.password }}</Label>
                    <TextLink :href="route('password.request')" class="text-sm" :tabindex="5">
                        {{ translations.forgot_password }}
                    </TextLink>
                </div>
                <a-input v-model:value="form.password" class="mt-2 w-full"
                    :placeholder="translations.password_placeholder || 'Enter Your Password'" />
                <InputError :message="form.errors.password" />
            </div>
            <div class="flex items-center justify-between" :tabindex="3">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" v-model:checked="form.remember" :tabindex="4" />
                    <span>{{ translations.remember_me }}</span>
                </Label>
            </div>
            <div>
                <Button type="submit" class="mt-4 w-full btn-primary" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ translations.login }}
                </Button>
            </div>
            <div class="text-center text-sm text-muted-foreground mt-4">
                {{ translations.no_account }}
                <TextLink :href="route('register')" :tabindex="5">{{
                    translations.sign_up
                    }}</TextLink>
            </div>
        </form>
    </a-modal>
</template>