<script setup lang="ts">
import LocaleSwitcher from '@/components/LocaleSwitcher.vue';
import { trans } from '@/lib/translation';
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="fixed top-4 right-4 z-50">
        <LocaleSwitcher />
    </div>
    <AuthLayout :title="trans(title)" :description="trans(description)">
        <slot />
    </AuthLayout>
</template>
