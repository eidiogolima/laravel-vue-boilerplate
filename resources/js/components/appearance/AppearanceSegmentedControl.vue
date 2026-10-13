<script setup lang="ts">
import { RadioGroupItem, RadioGroupRoot } from 'reka-ui';
import { computed } from 'vue';
import { appearanceOptions, useAppearance } from '@/composables/useAppearance';
import { trans } from '@/lib/translation';
import { cn } from '@/lib/utils';
import type { Appearance } from '@/types';

const { appearance, updateAppearance } = useAppearance();

const selectedIndex = computed(() =>
    Math.max(
        0,
        appearanceOptions.findIndex(
            (option) => option.value === appearance.value,
        ),
    ),
);
</script>

<template>
    <RadioGroupRoot
        :model-value="appearance"
        orientation="horizontal"
        :aria-label="trans('Theme')"
        class="relative grid w-full grid-cols-3 rounded-lg bg-black/5 p-0.5 dark:bg-white/10"
        @update:model-value="updateAppearance($event as Appearance)"
    >
        <span
            aria-hidden="true"
            class="absolute top-0.5 bottom-0.5 left-0.5 w-[calc((100%-0.25rem)/3)] rounded-md bg-white shadow-sm ring-1 ring-black/5 transition-transform duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] motion-reduce:transition-none dark:bg-neutral-600 dark:ring-white/10"
            :style="{ transform: `translateX(${selectedIndex * 100}%)` }"
        />
        <RadioGroupItem
            v-for="{ value, icon, label } in appearanceOptions"
            :key="value"
            :value="value"
            :title="trans(label)"
            :class="
                cn(
                    'relative z-10 flex h-7 cursor-pointer items-center justify-center gap-1.5 rounded-md text-xs font-medium transition-colors outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60',
                    appearance === value
                        ? 'text-foreground'
                        : 'text-muted-foreground hover:text-foreground',
                )
            "
            :data-test="`appearance-segment-${value}`"
        >
            <component :is="icon" class="size-3.5" />
            <span class="sr-only">{{ trans(label) }}</span>
        </RadioGroupItem>
    </RadioGroupRoot>
</template>
