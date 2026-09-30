<script setup lang="ts">
import type { ButtonVariants } from '@/components/ui/button';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    loading?: boolean;
    disabled?: boolean;
    type?: 'button' | 'submit' | 'reset';
    variant?: ButtonVariants['variant'];
    size?: ButtonVariants['size'];
};

withDefaults(defineProps<Props>(), {
    loading: false,
    disabled: false,
    type: 'submit',
});
</script>

<template>
    <Button
        :type="type"
        :variant="variant"
        :size="size"
        :disabled="loading || disabled"
        :aria-busy="loading"
    >
        <Spinner v-if="loading" />
        <slot />
    </Button>
</template>
