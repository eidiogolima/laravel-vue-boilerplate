<script setup lang="ts">
import { computed } from 'vue';

type Props = {
    values: number[];
};

const props = defineProps<Props>();

const viewBoxWidth = 100;
const viewBoxHeight = 32;
const verticalPadding = 2;

const linePath = computed(() => {
    const highestValue = Math.max(...props.values, 1);
    const lowestValue = Math.min(...props.values, 0);
    const valueRange = highestValue - lowestValue || 1;
    const step =
        props.values.length > 1 ? viewBoxWidth / (props.values.length - 1) : 0;

    return props.values
        .map((value, valueIndex) => {
            const x = valueIndex * step;
            const y =
                viewBoxHeight -
                verticalPadding -
                ((value - lowestValue) / valueRange) *
                    (viewBoxHeight - verticalPadding * 2);

            return `${valueIndex === 0 ? 'M' : 'L'}${x},${y}`;
        })
        .join(' ');
});

const areaPath = computed(
    () =>
        `${linePath.value} L${viewBoxWidth},${viewBoxHeight} L0,${viewBoxHeight} Z`,
);
</script>

<template>
    <svg
        :viewBox="`0 0 ${viewBoxWidth} ${viewBoxHeight}`"
        preserveAspectRatio="none"
        class="h-10 w-full"
        aria-hidden="true"
    >
        <path :d="areaPath" class="fill-chart-1/10" />
        <path
            :d="linePath"
            fill="none"
            stroke-width="2"
            stroke-linejoin="round"
            stroke-linecap="round"
            vector-effect="non-scaling-stroke"
            class="stroke-chart-1"
        />
    </svg>
</template>
