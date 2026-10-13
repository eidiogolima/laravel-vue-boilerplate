<script setup lang="ts">
import { Terminal } from '@lucide/vue';
import HttpMethodBadge from '@/components/admin/error-logs/HttpMethodBadge.vue';
import { trans } from '@/lib/translation';
import type { ErrorLogContext } from '@/types';

defineProps<{
    context: ErrorLogContext;
    method: string | null;
    url: string | null;
}>();
</script>

<template>
    <div v-if="context === 'http'" class="flex min-w-0 items-center gap-1.5">
        <HttpMethodBadge v-if="method" :method="method" />
        <span
            class="truncate font-mono text-xs text-muted-foreground"
            :title="url ?? ''"
        >
            {{ url }}
        </span>
    </div>
    <div v-else class="flex items-center gap-1.5 text-xs text-muted-foreground">
        <Terminal class="size-3.5" />
        {{ trans('Console / queue') }}
    </div>
</template>
