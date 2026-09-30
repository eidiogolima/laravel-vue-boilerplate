<script setup lang="ts">
import { computed, ref } from 'vue';
import { formatPercent } from '@/lib/formatters';

export type DonutChartSegment = {
    label: string;
    value: number;
};

type Props = {
    segments: DonutChartSegment[];
    totalLabel: string;
    valueFormatter: (value: number) => string;
};

const props = defineProps<Props>();

const segmentFillClasses = ['fill-chart-1', 'fill-chart-2', 'fill-chart-3'];
const segmentSwatchClasses = ['bg-chart-1', 'bg-chart-2', 'bg-chart-3'];

const chartSize = 200;
const center = chartSize / 2;
const outerRadius = 92;
const innerRadius = 64;
const fullCircle = Math.PI * 2;

const hoveredIndex = ref<number | null>(null);

const total = computed(() =>
    props.segments.reduce((sum, segment) => sum + segment.value, 0),
);

const pointOnCircle = (angle: number, radius: number): string =>
    `${center + radius * Math.cos(angle - Math.PI / 2)},${center + radius * Math.sin(angle - Math.PI / 2)}`;

const describeAnnularSector = (
    startAngle: number,
    endAngle: number,
): string => {
    const clampedEndAngle = Math.min(
        endAngle,
        startAngle + fullCircle - 0.0001,
    );
    const largeArcFlag = clampedEndAngle - startAngle > Math.PI ? 1 : 0;

    return [
        `M${pointOnCircle(startAngle, outerRadius)}`,
        `A${outerRadius},${outerRadius} 0 ${largeArcFlag} 1 ${pointOnCircle(clampedEndAngle, outerRadius)}`,
        `L${pointOnCircle(clampedEndAngle, innerRadius)}`,
        `A${innerRadius},${innerRadius} 0 ${largeArcFlag} 0 ${pointOnCircle(startAngle, innerRadius)}`,
        'Z',
    ].join(' ');
};

const arcs = computed(() => {
    let startAngle = 0;

    return props.segments.map((segment, segmentIndex) => {
        const share = total.value ? segment.value / total.value : 0;
        const endAngle = startAngle + share * fullCircle;
        const arc = {
            ...segment,
            share,
            path: describeAnnularSector(startAngle, endAngle),
            fillClass: segmentFillClasses[segmentIndex],
            swatchClass: segmentSwatchClasses[segmentIndex],
        };
        startAngle = endAngle;

        return arc;
    });
});

const highlightedArc = computed(() =>
    hoveredIndex.value === null ? null : arcs.value[hoveredIndex.value],
);
</script>

<template>
    <div class="flex w-full flex-col items-center gap-6">
        <div class="relative shrink-0">
            <svg
                :width="chartSize"
                :height="chartSize"
                :viewBox="`0 0 ${chartSize} ${chartSize}`"
                role="img"
                :aria-label="totalLabel"
                @pointerleave="hoveredIndex = null"
            >
                <path
                    v-for="(arc, arcIndex) in arcs"
                    :key="arc.label"
                    :d="arc.path"
                    stroke-width="2"
                    :class="[
                        arc.fillClass,
                        'stroke-card transition-opacity',
                        hoveredIndex !== null && hoveredIndex !== arcIndex
                            ? 'opacity-35'
                            : 'opacity-100',
                    ]"
                    @pointerenter="hoveredIndex = arcIndex"
                />
            </svg>
            <div
                class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center"
            >
                <span class="text-xs text-muted-foreground">
                    {{ highlightedArc?.label ?? totalLabel }}
                </span>
                <span class="text-lg font-semibold tabular-nums">
                    {{ valueFormatter(highlightedArc?.value ?? total) }}
                </span>
            </div>
        </div>

        <ul class="flex w-full flex-col gap-3 text-sm">
            <li
                v-for="(arc, arcIndex) in arcs"
                :key="arc.label"
                :class="[
                    'flex items-center gap-3 rounded-md px-2 py-1.5 transition-colors',
                    hoveredIndex === arcIndex && 'bg-muted',
                ]"
                @pointerenter="hoveredIndex = arcIndex"
                @pointerleave="hoveredIndex = null"
            >
                <span
                    :class="['size-2.5 shrink-0 rounded-full', arc.swatchClass]"
                />
                <span class="flex-1 text-muted-foreground">{{
                    arc.label
                }}</span>
                <span class="font-medium tabular-nums">
                    {{ valueFormatter(arc.value) }}
                </span>
                <span
                    class="w-12 text-right text-muted-foreground tabular-nums"
                >
                    {{ formatPercent(arc.share) }}
                </span>
            </li>
        </ul>
    </div>
</template>
