<script setup lang="ts">
import { trans } from '@/lib/translation';
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import GoogleSignInButton from '@/components/GoogleSignInButton.vue';
import AuthDivider from '@/components/AuthDivider.vue';

defineProps<{
    passwordRules: string;
    canLogInWithGoogle: boolean;
}>();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head :title="trans('Register')" />

    <div v-if="canLogInWithGoogle" class="flex flex-col gap-6">
        <GoogleSignInButton />
        <AuthDivider :label="trans('Or continue with email')" />
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="name">{{ trans('Name') }}</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    class="h-10"
                    :placeholder="trans('Full name')"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ trans('Email address') }}</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    class="h-10"
                    :placeholder="trans('email@example.com')"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">{{ trans('Password') }}</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    class="h-10"
                    :placeholder="trans('Password')"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">{{
                    trans('Confirm password')
                }}</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    class="h-10"
                    :placeholder="trans('Confirm password')"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                size="lg"
                class="mt-1 w-full"
                :tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                {{ trans('Create account') }}
            </Button>
        </div>

        <p class="text-center text-sm text-muted-foreground">
            {{ trans('Already have an account?') }}
            <TextLink :href="login()" :tabindex="6">{{
                trans('Log in')
            }}</TextLink>
        </p>
    </Form>
</template>
