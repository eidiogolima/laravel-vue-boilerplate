<script setup lang="ts">
import { useElementSize } from '@vueuse/core';
import { computed, ref, useTemplateRef } from 'vue';
import { buildTickValues, calculateNiceMaximum } from '@/lib/chartScale';

export type AreaChartPoint = {
    label: string;
    value: number;
    comparisonValue?: number;
};

type Props = {
    points: AreaChartPoint[];
    seriesLabel: string;
    comparisonLabel?: string;
    valueFormatter: (value: number) => string;
    axisFormatter: (value: number) => string;
    height?: number;
};

const props = withDefaults(defineProps<Props>(), {
    height: 280,
});

const padding = { top: 16, right: 16, bottom: 32, left: 64 };
const minimumSpacePerXAxisLabel = 72;

const container = useTemplateRef<HTMLElement>('container');
const { width } = useElementSize(container);
const hoveredIndex = ref<number | null>(null);

const plotWidth = computed(() =>
    Math.max(width.value - padding.left - padding.right, 0),
);
const plotHeight = computed(() => props.height - padding.top - padding.bottom);
const baselineY = computed(() => padding.top + plotHeight.value);
const hasComparison = computed(() =>
    props.points.some((point) => point.comparisonValue !== undefined),
);
const maximumValue = computed(() =>
    calculateNiceMaximum(
        Math.max(
            ...props.points.map((point) =>
                Math.max(point.value, point.comparisonValue ?? 0),
            ),
            0,
        ),
    ),
);
const yTicks = computed(() => buildTickValues(maximumValue.value));
const xLabelIndexes = computed(() => {
    const maximumXAxisLabels = Math.max(
        2,
        Math.floor(plotWidth.value / minimumSpacePerXAxisLabel),
    );
    const step = Math.max(
        1,
        Math.ceil(props.points.length / maximumXAxisLabels),
    );

    return props.points
        .map((_, pointIndex) => pointIndex)
        .filter((pointIndex) => pointIndex % step === 0);
});

const xPosition = (pointIndex: number): number =>
    props.points.length <= 1
        ? padding.left + plotWidth.value / 2
        : padding.left +
          (pointIndex * plotWidth.value) / (props.points.length - 1);

const yPosition = (value: number): number =>
    padding.top +
    plotHeight.value -
    (value / maximumValue.value) * plotHeight.value;

const buildLinePath = (values: number[]): string =>
    values
        .map(
            (value, pointIndex) =>
                `${pointIndex === 0 ? 'M' : 'L'}${xPosition(pointIndex)},${yPosition(value)}`,
        )
        .join(' ');

const seriesLinePath = computed(() =>
    buildLinePath(props.points.map((point) => point.value)),
);
const seriesAreaPath = computed(
    () =>
        `${seriesLinePath.value} L${xPosition(props.points.length - 1)},${baselineY.value} L${xPosition(0)},${baselineY.value} Z`,
);
const comparisonLinePath = computed(() =>
    buildLinePath(props.points.map((point) => point.comparisonValue ?? 0)),
);

const hoveredPoint = computed(() =>
    hoveredIndex.value === null ? null : props.points[hoveredIndex.value],
);
const tooltipStyle = computed(() => {
    if (hoveredIndex.value === null) {
        return {};
    }

    const pointX = xPosition(hoveredIndex.value);
    const isOnRightHalf = pointX > width.value / 2;

    return {
        left: `${pointX}px`,
        transform: isOnRightHalf
            ? 'translateX(calc(-100% - 12px))'
            : 'translateX(12px)',
    };
});

const updateHoveredIndex = (event: PointerEvent): void => {
    if (!props.points.length || !container.value) {
        return;
    }

    const relativeX =
        event.clientX - container.value.getBoundingClientRect().left;
    const step =
        props.points.length > 1
            ? plotWidth.value / (props.points.length - 1)
            : 1;
    const nearestIndex = Math.round((relativeX - padding.left) / step);

    hoveredIndex.value = Math.min(
        Math.max(nearestIndex, 0),
        props.points.length - 1,
    );
};

const clearHoveredIndex = (): void => {
    hoveredIndex.value = null;
};
</script>

<template>
    <div ref="container" class="relative w-full">
        <div
            v-if="hasComparison && comparisonLabel"
            class="mb-3 flex flex-wrap items-center gap-4 text-xs text-muted-foreground"
        >
            <span class="flex items-center gap-2">
                <span class="h-0.5 w-4 rounded-full bg-chart-1" />
                {{ seriesLabel }}
            </span>
            <span class="flex items-center gap-2">
                <span class="h-0.5 w-4 rounded-full bg-muted-foreground/50" />
                {{ comparisonLabel }}
            </span>
        </div>

        <svg
            v-if="width > 0"
            :width="width"
            :height="height"
            role="img"
            :aria-label="seriesLabel"
            class="touch-none overflow-visible select-none"
            @pointermove="updateHoveredIndex"
            @pointerleave="clearHoveredIndex"
        >
            <g>
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
                        :x="padding.left - 10"
                        :y="yPosition(tick)"
                        text-anchor="end"
                        dominant-baseline="middle"
                        class="fill-muted-foreground text-[11px] tabular-nums"
                    >
                        {{ axisFormatter(tick) }}
                    </text>
                </template>
                <text
                    v-for="labelIndex in xLabelIndexes"
                    :key="labelIndex"
                    :x="xPosition(labelIndex)"
                    :y="height - 8"
                    text-anchor="middle"
                    class="fill-muted-foreground text-[11px]"
                >
                    {{ points[labelIndex].label }}
                </text>
            </g>

            <path
                v-if="hasComparison"
                :d="comparisonLinePath"
                fill="none"
                stroke-width="2"
                stroke-linejoin="round"
                stroke-linecap="round"
                class="stroke-muted-foreground/40"
            />
            <path :d="seriesAreaPath" class="fill-chart-1/10" />
            <path
                :d="seriesLinePath"
                fill="none"
                stroke-width="2"
                stroke-linejoin="round"
                stroke-linecap="round"
                class="stroke-chart-1"
            />

            <g v-if="hoveredPoint && hoveredIndex !== null">
                <line
                    :x1="xPosition(hoveredIndex)"
                    :x2="xPosition(hoveredIndex)"
                    :y1="padding.top"
                    :y2="baselineY"
                    stroke-width="1"
                    class="stroke-muted-foreground/40"
                />
                <circle
                    v-if="hoveredPoint.comparisonValue !== undefined"
                    :cx="xPosition(hoveredIndex)"
                    :cy="yPosition(hoveredPoint.comparisonValue)"
                    r="4"
                    stroke-width="2"
                    class="fill-muted-foreground stroke-card"
                />
                <circle
                    :cx="xPosition(hoveredIndex)"
                    :cy="yPosition(hoveredPoint.value)"
                    r="5"
                    stroke-width="2"
                    class="fill-chart-1 stroke-card"
                />
            </g>
        </svg>

        <div
            v-if="hoveredPoint"
            class="pointer-events-none absolute top-10 z-10 min-w-40 rounded-lg border bg-popover px-3 py-2 text-xs shadow-lg"
            :style="tooltipStyle"
        >
            <p class="mb-1.5 font-medium text-foreground">
                {{ hoveredPoint.label }}
            </p>
            <p class="flex items-center justify-between gap-4">
                <span class="flex items-center gap-2 text-muted-foreground">
                    <span class="size-2 rounded-full bg-chart-1" />
                    {{ seriesLabel }}
                </span>
                <span class="font-medium text-foreground tabular-nums">
                    {{ valueFormatter(hoveredPoint.value) }}
                </span>
            </p>
            <p
                v-if="hoveredPoint.comparisonValue !== undefined"
                class="mt-1 flex items-center justify-between gap-4"
            >
                <span class="flex items-center gap-2 text-muted-foreground">
                    <span class="size-2 rounded-full bg-muted-foreground" />
                    {{ comparisonLabel }}
                </span>
                <span class="font-medium text-foreground tabular-nums">
                    {{ valueFormatter(hoveredPoint.comparisonValue) }}
                </span>
            </p>
        </div>

        <table class="sr-only">
            <caption>
                {{
                    seriesLabel
                }}
            </caption>
            <tbody>
                <tr v-for="point in points" :key="point.label">
                    <th scope="row">{{ point.label }}</th>
                    <td>{{ valueFormatter(point.value) }}</td>
                    <td v-if="point.comparisonValue !== undefined">
                        {{ valueFormatter(point.comparisonValue) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
