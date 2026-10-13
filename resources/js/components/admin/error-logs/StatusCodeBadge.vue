<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    statusCode: number;
}>();

const colorClass = computed((): string => {
    if (props.statusCode >= 500) {
        return 'bg-red-500/10 text-red-700 dark:text-red-400';
    }

    if (props.statusCode >= 400) {
        return 'bg-amber-500/10 text-amber-700 dark:text-amber-400';
    }

    return 'bg-muted text-muted-foreground';
});
</script>

<template>
    <span
        class="inline-flex rounded-md px-1.5 py-0.5 font-mono text-xs font-semibold tabular-nums"
        :class="colorClass"
    >
        {{ statusCode }}
    </span>
</template>
