<script setup lang="ts">
import { trans } from '@/lib/translation';
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import GoogleSignInButton from '@/components/GoogleSignInButton.vue';
import AuthDivider from '@/components/AuthDivider.vue';
import { Passkeys } from '@laravel/passkeys';
import { CircleCheck } from '@lucide/vue';
import { onMounted, ref } from 'vue';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canLogInWithGoogle: boolean;
}>();

const arePasskeysSupported = ref(false);

onMounted(() => {
    arePasskeysSupported.value = Passkeys.isSupported();
});
</script>

<template>
    <Head :title="trans('Log in')" />

    <div
        v-if="status"
        class="flex items-center gap-2 rounded-lg border border-green-600/20 bg-green-600/10 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-400"
    >
        <CircleCheck class="size-4 shrink-0" />
        {{ status }}
    </div>

    <div
        v-if="canLogInWithGoogle || arePasskeysSupported"
        class="flex flex-col gap-6"
    >
        <div class="flex flex-col gap-2">
            <GoogleSignInButton v-if="canLogInWithGoogle" />
            <PasskeyVerify :show-divider="false" />
        </div>
        <AuthDivider :label="trans('Or continue with email')" />
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="email">{{ trans('Email address') }}</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    class="h-10"
                    :placeholder="trans('email@example.com')"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">{{ trans('Password') }}</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        {{ trans('Forgot your password?') }}
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    class="h-10"
                    :placeholder="trans('Password')"
                />
                <InputError :message="errors.password" />
            </div>

            <Label for="remember" class="flex items-center gap-3 font-normal">
                <Checkbox id="remember" name="remember" :tabindex="3" />
                <span>{{ trans('Remember me') }}</span>
            </Label>

            <Button
                type="submit"
                size="lg"
                class="w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                {{ trans('Log in') }}
            </Button>
        </div>

        <p class="text-center text-sm text-muted-foreground">
            {{ trans("Don't have an account?") }}
            <TextLink :href="register()" :tabindex="6">{{
                trans('Sign up')
            }}</TextLink>
        </p>
    </Form>
</template>
