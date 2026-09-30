<script setup lang="ts">
import { TrendingDown, TrendingUp } from '@lucide/vue';
import type { Component } from 'vue';
import { computed } from 'vue';
import Sparkline from '@/components/charts/Sparkline.vue';
import { Card } from '@/components/ui/card';
import { formatSignedPercent } from '@/lib/formatters';
import { trans } from '@/lib/translation';

type Props = {
    label: string;
    value: string;
    icon: Component;
    changeRatio: number | null;
    trendValues: number[];
};

const props = defineProps<Props>();

const isPositiveChange = computed(() => (props.changeRatio ?? 0) >= 0);
</script>

<template>
    <Card class="gap-4 py-5">
        <div class="flex items-start justify-between gap-4 px-5">
            <div class="flex flex-col gap-1">
                <span class="text-sm text-muted-foreground">{{ label }}</span>
                <span
                    class="text-2xl font-semibold tracking-tight tabular-nums"
                >
                    {{ value }}
                </span>
            </div>
            <div
                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
            >
                <component :is="icon" class="size-4 text-muted-foreground" />
            </div>
        </div>
        <div class="flex items-center gap-2 px-5 text-xs">
            <span
                v-if="changeRatio !== null"
                :class="[
                    'inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 font-medium tabular-nums',
                    isPositiveChange
                        ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                        : 'bg-red-500/10 text-red-700 dark:text-red-400',
                ]"
            >
                <TrendingUp v-if="isPositiveChange" class="size-3.5" />
                <TrendingDown v-else class="size-3.5" />
                {{ formatSignedPercent(changeRatio) }}
            </span>
            <span class="text-muted-foreground">
                {{ trans('vs previous period') }}
            </span>
        </div>
        <div class="px-5">
            <Sparkline :values="trendValues" />
        </div>
    </Card>
</template>
