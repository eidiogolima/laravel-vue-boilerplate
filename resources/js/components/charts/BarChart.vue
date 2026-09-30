<script setup lang="ts">
import { useElementSize } from '@vueuse/core';
import { computed, ref, useTemplateRef } from 'vue';
import { buildTickValues, calculateNiceMaximum } from '@/lib/chartScale';

export type BarChartItem = {
    label: string;
    value: number;
};

type Props = {
    items: BarChartItem[];
    seriesLabel: string;
    valueFormatter: (value: number) => string;
    height?: number;
};

const props = withDefaults(defineProps<Props>(), {
    height: 240,
});

const padding = { top: 24, right: 8, bottom: 28, left: 40 };
const maximumBarWidth = 24;
const cornerRadius = 4;

const container = useTemplateRef<HTMLElement>('container');
const { width } = useElementSize(container);
const hoveredIndex = ref<number | null>(null);

const plotWidth = computed(() =>
    Math.max(width.value - padding.left - padding.right, 0),
);
const plotHeight = computed(() => props.height - padding.top - padding.bottom);
const bandWidth = computed(() =>
    props.items.length ? plotWidth.value / props.items.length : 0,
);
const barWidth = computed(() =>
    Math.min(maximumBarWidth, bandWidth.value * 0.6),
);
const maximumValue = computed(() =>
    calculateNiceMaximum(Math.max(...props.items.map((item) => item.value), 0)),
);
const yTicks = computed(() => buildTickValues(maximumValue.value));
const highestItemIndex = computed(() =>
    props.items.reduce(
        (highestIndex, item, itemIndex) =>
            item.value > props.items[highestIndex].value
                ? itemIndex
                : highestIndex,
        0,
    ),
);

const yPosition = (value: number): number =>
    padding.top +
    plotHeight.value -
    (value / maximumValue.value) * plotHeight.value;

const bandCenter = (itemIndex: number): number =>
    padding.left + bandWidth.value * itemIndex + bandWidth.value / 2;

const buildBarPath = (itemIndex: number, value: number): string => {
    const left = bandCenter(itemIndex) - barWidth.value / 2;
    const right = left + barWidth.value;
    const top = yPosition(value);
    const bottom = padding.top + plotHeight.value;
    const radius = Math.min(cornerRadius, bottom - top, barWidth.value / 2);

    return `M${left},${bottom} L${left},${top + radius} Q${left},${top} ${left + radius},${top} L${right - radius},${top} Q${right},${top} ${right},${top + radius} L${right},${bottom} Z`;
};

const hoveredItem = computed(() =>
    hoveredIndex.value === null ? null : props.items[hoveredIndex.value],
);
const tooltipStyle = computed(() =>
    hoveredIndex.value === null
        ? {}
        : {
              left: `${bandCenter(hoveredIndex.value)}px`,
              top: `${yPosition(props.items[hoveredIndex.value].value) - 8}px`,
          },
);
</script>

<template>
    <div ref="container" class="relative w-full">
        <svg
            v-if="width > 0"
            :width="width"
            :height="height"
            role="img"
            :aria-label="seriesLabel"
            class="overflow-visible select-none"
            @pointerleave="hoveredIndex = null"
        >
            <template v-for="tick in yTicks" :key="tick">
                <line
                    :x1="padding.left"
                    :x2="width - padding.right"
                    :y1="yPosition(tick)"
                    :y2="yPosition(tick)"
                    class="stroke-border"
                    stroke-width="1"
                />
                <text
                    :x="padding.left - 8"
                    :y="yPosition(tick)"
                    text-anchor="end"
                    dominant-baseline="middle"
                    class="fill-muted-foreground text-[11px] tabular-nums"
                >
                    {{ valueFormatter(tick) }}
                </text>
            </template>

            <g v-for="(item, itemIndex) in items" :key="item.label">
                <path
                    :d="buildBarPath(itemIndex, item.value)"
                    :class="[
                        'fill-chart-1 transition-opacity',
                        hoveredIndex !== null && hoveredIndex !== itemIndex
                            ? 'opacity-40'
                            : 'opacity-100',
                    ]"
                />
                <text
                    v-if="
                        itemIndex === highestItemIndex && hoveredIndex === null
                    "
                    :x="bandCenter(itemIndex)"
                    :y="yPosition(item.value) - 8"
                    text-anchor="middle"
                    class="fill-foreground text-[11px] font-medium tabular-nums"
                >
                    {{ valueFormatter(item.value) }}
                </text>
                <text
                    :x="bandCenter(itemIndex)"
                    :y="height - 8"
                    text-anchor="middle"
                    class="fill-muted-foreground text-[11px] capitalize"
                >
                    {{ item.label }}
                </text>
                <rect
                    :x="padding.left + bandWidth * itemIndex"
                    :y="padding.top"
                    :width="bandWidth"
                    :height="plotHeight"
                    fill="transparent"
                    @pointerenter="hoveredIndex = itemIndex"
                />
            </g>
        </svg>

        <div
            v-if="hoveredItem"
            class="pointer-events-none absolute z-10 -translate-x-1/2 -translate-y-full rounded-lg border bg-popover px-3 py-2 text-xs shadow-lg"
            :style="tooltipStyle"
        >
            <p class="font-medium text-foreground capitalize">
                {{ hoveredItem.label }}
            </p>
            <p class="text-muted-foreground tabular-nums">
                {{ seriesLabel }}:
                <span class="font-medium text-foreground">{{
                    valueFormatter(hoveredItem.value)
                }}</span>
            </p>
        </div>

        <table class="sr-only">
            <caption>
                {{
                    seriesLabel
                }}
            </caption>
            <tbody>
                <tr v-for="item in items" :key="item.label">
                    <th scope="row">{{ item.label }}</th>
                    <td>{{ valueFormatter(item.value) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
