<script setup lang="ts">
import { CircleCheck, CircleX, Info, TriangleAlert } from '@lucide/vue';
import type { LucideIcon } from '@lucide/vue';
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { NotificationLevel } from '@/types';

const { level } = defineProps<{
    level: NotificationLevel;
}>();

const levelAppearances: Record<
    NotificationLevel,
    { icon: LucideIcon; classes: string }
> = {
    info: {
        icon: Info,
        classes: 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
    },
    success: {
        icon: CircleCheck,
        classes: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    },
    warning: {
        icon: TriangleAlert,
        classes: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
    },
    error: {
        icon: CircleX,
        classes: 'bg-red-500/10 text-red-600 dark:text-red-400',
    },
};

const appearance = computed(
    () => levelAppearances[level] ?? levelAppearances.info,
);
</script>

<template>
    <span
        :class="
            cn(
                'flex size-8 shrink-0 items-center justify-center rounded-full',
                appearance.classes,
            )
        "
    >
        <component :is="appearance.icon" class="size-4" />
    </span>
</template>
