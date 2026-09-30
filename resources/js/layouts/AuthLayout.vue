<script setup lang="ts">
import { trans } from '@/lib/translation';
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthLayout :title="trans(title)" :description="trans(description)">
        <slot />
    </AuthLayout>
</template>
