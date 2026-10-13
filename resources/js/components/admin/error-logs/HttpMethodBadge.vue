<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    method: string;
}>();

const methodColorClasses: Record<string, string> = {
    GET: 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
    POST: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
    PUT: 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
    PATCH: 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
    DELETE: 'bg-red-500/10 text-red-700 dark:text-red-400',
};

const colorClass = computed(
    (): string =>
        methodColorClasses[props.method] ?? 'bg-muted text-muted-foreground',
);
</script>

<template>
    <span
        class="shrink-0 rounded px-1 py-px font-mono text-[10px] font-bold"
        :class="colorClass"
    >
        {{ method }}
    </span>
</template>
