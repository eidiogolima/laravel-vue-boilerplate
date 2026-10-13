<script setup lang="ts">
type Props = {
    size?: 'full' | 'narrow';
};

const { size = 'full' } = defineProps<Props>();
</script>

<template>
    <div
        class="flex w-full flex-col gap-6 p-4 md:p-6"
        :class="{ 'max-w-3xl': size === 'narrow' }"
    >
        <slot />
    </div>
</template>
