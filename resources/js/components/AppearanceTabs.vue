<script setup lang="ts">
import { RadioGroupItem, RadioGroupRoot } from 'reka-ui';
import AppearanceWindowPreview from '@/components/appearance/AppearanceWindowPreview.vue';
import { appearanceOptions, useAppearance } from '@/composables/useAppearance';
import { trans } from '@/lib/translation';
import { cn } from '@/lib/utils';
import type { Appearance } from '@/types';

const { appearance, updateAppearance } = useAppearance();
</script>

<template>
    <RadioGroupRoot
        :model-value="appearance"
        orientation="horizontal"
        :aria-label="trans('Theme')"
        class="grid max-w-md grid-cols-3 gap-4"
        @update:model-value="updateAppearance($event as Appearance)"
    >
        <RadioGroupItem
            v-for="{ value, label } in appearanceOptions"
            :key="value"
            :value="value"
            class="group flex cursor-pointer flex-col items-center gap-2 rounded-xl outline-none"
            :data-test="`appearance-option-${value}`"
        >
            <span
                :class="
                    cn(
                        'relative aspect-[16/11] w-full overflow-hidden rounded-lg ring-1 ring-black/10 transition-all duration-200 ease-out dark:ring-white/15',
                        'ring-offset-2 ring-offset-background group-hover:shadow-md group-focus-visible:ring-2 group-focus-visible:ring-blue-500/60',
                        appearance === value &&
                            'ring-[3px] ring-blue-500 group-focus-visible:ring-[3px] group-focus-visible:ring-blue-500 dark:ring-blue-500',
                    )
                "
            >
                <template v-if="value === 'system'">
                    <AppearanceWindowPreview
                        theme="light"
                        class="[clip-path:polygon(0_0,100%_0,0_100%)]"
                    />
                    <AppearanceWindowPreview
                        theme="dark"
                        class="[clip-path:polygon(100%_0,100%_100%,0_100%)]"
                    />
                </template>
                <AppearanceWindowPreview v-else :theme="value" />
            </span>
            <span
                :class="
                    cn(
                        'text-xs transition-colors',
                        appearance === value
                            ? 'font-semibold text-foreground'
                            : 'text-muted-foreground group-hover:text-foreground',
                    )
                "
            >
                {{ trans(label) }}
            </span>
        </RadioGroupItem>
    </RadioGroupRoot>
</template>
