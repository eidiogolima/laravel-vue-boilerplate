<script setup lang="ts">
import { trans } from '@/lib/translation';
import type { UrlMethodPair } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';
import { usePasskeyVerify } from '@laravel/passkeys/vue';
import { KeyRound } from '@lucide/vue';
import AuthDivider from '@/components/AuthDivider.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    routes?: {
        options: UrlMethodPair;
        submit: UrlMethodPair;
    };
    label?: string;
    loadingLabel?: string;
    separator?: string;
    showDivider?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    showDivider: true,
});

const { verify, isLoading, error, isSupported } = usePasskeyVerify({
    ...(props.routes
        ? {
              routes: {
                  options: props.routes.options.url,
                  submit: props.routes.submit.url,
              },
          }
        : {}),
    onSuccess: (response) => {
        router.visit(response.redirect ?? '/dashboard');
    },
});
</script>

<template>
    <div v-if="isSupported">
        <div class="grid gap-2">
            <Button
                type="button"
                variant="outline"
                class="w-full"
                @click="verify"
                :disabled="isLoading"
            >
                <Spinner v-if="isLoading" />
                <KeyRound v-else class="h-4 w-4" />
                {{
                    isLoading
                        ? (props.loadingLabel ?? trans('Authenticating...'))
                        : (props.label ?? trans('Sign in with a passkey'))
                }}
            </Button>

            <div v-if="error" class="text-center">
                <InputError :message="trans(error)" />
            </div>
        </div>

        <AuthDivider
            v-if="showDivider"
            class="my-6"
            :label="props.separator ?? trans('Or continue with email')"
        />
    </div>
</template>
