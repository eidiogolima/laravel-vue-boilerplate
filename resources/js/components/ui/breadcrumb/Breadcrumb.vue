<script lang="ts" setup>
import { trans } from "@/lib/translation"
import type { HTMLAttributes } from "vue"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()
</script>

<template>
  <nav
    :aria-label="trans('Breadcrumb')"
    data-slot="breadcrumb"
    :class="props.class"
  >
    <slot />
  </nav>
</template>
