<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="currentColor"
        :class="className"
        v-bind="$attrs"
    >
        <rect
            x="2"
            y="13"
            width="5.5"
            height="9"
            rx="1.5"
            fill-opacity="0.45"
        />
        <rect
            x="9.25"
            y="7.5"
            width="5.5"
            height="14.5"
            rx="1.5"
            fill-opacity="0.7"
        />
        <rect x="16.5" y="2" width="5.5" height="20" rx="1.5" />
    </svg>
</template>
