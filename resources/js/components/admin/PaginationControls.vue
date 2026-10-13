<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { formatNumber } from '@/lib/formatters';
import { trans } from '@/lib/translation';
import type { PaginatedResource } from '@/types';

defineProps<{
    paginator: PaginatedResource<unknown>;
}>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-3 text-sm text-muted-foreground sm:flex-row"
    >
        <span class="tabular-nums">
            {{
                trans('Showing :from–:to of :total', {
                    from: formatNumber(paginator.meta.from ?? 0),
                    to: formatNumber(paginator.meta.to ?? 0),
                    total: formatNumber(paginator.meta.total),
                })
            }}
        </span>
        <div class="flex items-center gap-2">
            <Button
                v-if="paginator.links.prev"
                variant="outline"
                size="icon-sm"
                as-child
            >
                <Link
                    :href="paginator.links.prev"
                    :aria-label="trans('Previous page')"
                    preserve-scroll
                    preserve-state
                >
                    <ChevronLeft />
                </Link>
            </Button>
            <Button
                v-else
                variant="outline"
                size="icon-sm"
                disabled
                :aria-label="trans('Previous page')"
            >
                <ChevronLeft />
            </Button>
            <span class="tabular-nums">
                {{
                    trans('Page :current of :last', {
                        current: paginator.meta.current_page,
                        last: paginator.meta.last_page,
                    })
                }}
            </span>
            <Button
                v-if="paginator.links.next"
                variant="outline"
                size="icon-sm"
                as-child
            >
                <Link
                    :href="paginator.links.next"
                    :aria-label="trans('Next page')"
                    preserve-scroll
                    preserve-state
                >
                    <ChevronRight />
                </Link>
            </Button>
            <Button
                v-else
                variant="outline"
                size="icon-sm"
                disabled
                :aria-label="trans('Next page')"
            >
                <ChevronRight />
            </Button>
        </div>
    </div>
</template>
