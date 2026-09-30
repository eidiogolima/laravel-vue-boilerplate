<script setup lang="ts">
import { CircleCheck, CircleX, Clock, RotateCcw } from '@lucide/vue';
import type { Component } from 'vue';
import { computed } from 'vue';
import { trans } from '@/lib/translation';
import type { OrderStatus } from '@/types';

type Props = {
    status: OrderStatus;
};

const props = defineProps<Props>();

const statusPresentations: Record<
    OrderStatus,
    { label: string; icon: Component; classes: string }
> = {
    paid: {
        label: 'Paid',
        icon: CircleCheck,
        classes: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
    },
    pending: {
        label: 'Pending',
        icon: Clock,
        classes: 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
    },
    refunded: {
        label: 'Refunded',
        icon: RotateCcw,
        classes: 'bg-muted text-muted-foreground',
    },
    failed: {
        label: 'Failed',
        icon: CircleX,
        classes: 'bg-red-500/10 text-red-700 dark:text-red-400',
    },
};

const presentation = computed(() => statusPresentations[props.status]);
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium',
            presentation.classes,
        ]"
    >
        <component :is="presentation.icon" class="size-3.5" />
        {{ trans(presentation.label) }}
    </span>
</template>
