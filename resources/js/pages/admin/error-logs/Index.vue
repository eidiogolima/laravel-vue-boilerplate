<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Activity,
    AlertTriangle,
    Bug,
    CheckCircle2,
    ChevronRight,
    FlaskConical,
    Layers,
    Search,
} from '@lucide/vue';
import { useDebounceFn } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import PageContainer from '@/components/PageContainer.vue';
import ErrorLogRequestSummary from '@/components/admin/error-logs/ErrorLogRequestSummary.vue';
import StatusCodeBadge from '@/components/admin/error-logs/StatusCodeBadge.vue';
import PaginationControls from '@/components/admin/PaginationControls.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatDateTime, formatNumber } from '@/lib/formatters';
import { trans } from '@/lib/translation';
import { index, show, testError } from '@/routes/admin/error-logs';
import type {
    ErrorLogFilters,
    ErrorLogListItem,
    ErrorLogResolution,
    ErrorLogSummary,
    PaginatedResource,
} from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Error logs', href: index() }],
    },
});

const props = defineProps<{
    errorLogs: PaginatedResource<ErrorLogListItem>;
    summary: ErrorLogSummary;
    filters: ErrorLogFilters;
}>();

const allStatusCodesFilter = 'all';

const searchTerm = ref(props.filters.search);
const selectedStatus = ref<string>(
    props.filters.status || allStatusCodesFilter,
);
const selectedSort = ref<string>(props.filters.sort);

const applyFilters = (resolution: ErrorLogResolution): void => {
    router.get(
        index.url(),
        {
            search: searchTerm.value.trim() || undefined,
            resolution,
            status:
                selectedStatus.value === allStatusCodesFilter
                    ? undefined
                    : selectedStatus.value,
            sort: selectedSort.value,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const applyCurrentFilters = (): void => applyFilters(props.filters.resolution);

const applyFiltersDebounced = useDebounceFn(applyCurrentFilters, 300);

watch(searchTerm, applyFiltersDebounced);
watch([selectedStatus, selectedSort], applyCurrentFilters);

const resolutionTabs = computed(() => [
    {
        value: 'open' as const,
        text: trans('Unresolved'),
        count: props.summary.open,
    },
    {
        value: 'resolved' as const,
        text: trans('Resolved'),
        count: props.summary.resolved,
    },
    {
        value: 'all' as const,
        text: trans('All errors'),
        count: props.summary.total,
    },
]);

const summaryTiles = computed(() => [
    {
        text: trans('Last 24 hours'),
        value: props.summary.last_24_hours,
        icon: Activity,
    },
    {
        text: trans('Unresolved'),
        value: props.summary.open,
        icon: AlertTriangle,
    },
    {
        text: trans('Distinct open errors'),
        value: props.summary.distinct_open,
        icon: Layers,
    },
]);

const isShowingCleanSlate = computed(
    () => props.filters.resolution === 'open' && !props.filters.search,
);
</script>

<template>
    <Head :title="trans('Error logs')" />

    <PageContainer>
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                :title="trans('Error logs')"
                :description="
                    trans(
                        'Exceptions captured by the system, with request, user and stack trace.',
                    )
                "
            />
            <Button variant="outline" as-child>
                <a
                    :href="testError.url()"
                    target="_blank"
                    rel="noopener"
                    data-test="trigger-test-error-link"
                >
                    <FlaskConical />
                    {{ trans('Trigger test error') }}
                </a>
            </Button>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <Card
                v-for="tile in summaryTiles"
                :key="tile.text"
                class="flex-row items-center gap-3 px-4 py-3"
            >
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
                >
                    <component
                        :is="tile.icon"
                        class="size-4 text-muted-foreground"
                    />
                </div>
                <div class="min-w-0">
                    <p class="text-xl leading-tight font-semibold tabular-nums">
                        {{ formatNumber(tile.value) }}
                    </p>
                    <p class="truncate text-xs text-muted-foreground">
                        {{ tile.text }}
                    </p>
                </div>
            </Card>
        </div>

        <Card>
            <CardContent class="flex flex-col gap-4">
                <div
                    class="flex w-fit gap-1 rounded-lg bg-muted p-1"
                    role="tablist"
                >
                    <button
                        v-for="tab in resolutionTabs"
                        :key="tab.value"
                        type="button"
                        role="tab"
                        :aria-selected="filters.resolution === tab.value"
                        class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            filters.resolution === tab.value
                                ? 'bg-background text-foreground shadow-xs'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        @click="applyFilters(tab.value)"
                    >
                        {{ tab.text }}
                        <span class="text-xs tabular-nums opacity-70">
                            {{ formatNumber(tab.count) }}
                        </span>
                    </button>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchTerm"
                            type="search"
                            class="pl-9"
                            :placeholder="
                                trans(
                                    'Search by message, exception, URL or file',
                                )
                            "
                            :aria-label="
                                trans(
                                    'Search by message, exception, URL or file',
                                )
                            "
                        />
                    </div>
                    <Select v-model="selectedStatus">
                        <SelectTrigger
                            class="w-full sm:w-44"
                            :aria-label="trans('Status code')"
                        >
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="allStatusCodesFilter">
                                {{ trans('All status codes') }}
                            </SelectItem>
                            <SelectItem value="5xx">
                                {{ trans('5xx (server)') }}
                            </SelectItem>
                            <SelectItem value="4xx">
                                {{ trans('4xx (client)') }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="selectedSort">
                        <SelectTrigger
                            class="w-full sm:w-40"
                            :aria-label="trans('Sort')"
                        >
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="newest">
                                {{ trans('Newest first') }}
                            </SelectItem>
                            <SelectItem value="oldest">
                                {{ trans('Oldest first') }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="overflow-x-auto rounded-lg border">
                    <table class="w-full text-sm">
                        <thead
                            class="bg-muted/50 text-left text-muted-foreground"
                        >
                            <tr>
                                <th class="w-16 px-4 py-3 font-medium">
                                    {{ trans('Code') }}
                                </th>
                                <th class="px-4 py-3 font-medium">
                                    {{ trans('Error') }}
                                </th>
                                <th
                                    class="hidden w-72 px-4 py-3 font-medium lg:table-cell"
                                >
                                    {{ trans('Request') }}
                                </th>
                                <th
                                    class="hidden w-36 px-4 py-3 font-medium md:table-cell"
                                >
                                    {{ trans('When') }}
                                </th>
                                <th class="w-10 px-4 py-3">
                                    <span class="sr-only">{{
                                        trans('Actions')
                                    }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="errorLog in errorLogs.data"
                                :key="errorLog.id"
                                class="cursor-pointer align-top transition-colors hover:bg-muted/40"
                                @click="router.visit(show(errorLog.id))"
                            >
                                <td class="px-4 py-3">
                                    <StatusCodeBadge
                                        :status-code="errorLog.status_code"
                                    />
                                </td>
                                <td class="max-w-0 px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="truncate font-mono text-xs font-semibold"
                                        >
                                            {{ errorLog.exception }}
                                        </span>
                                        <Badge
                                            v-if="errorLog.is_resolved"
                                            variant="secondary"
                                        >
                                            {{ trans('Resolved') }}
                                        </Badge>
                                    </div>
                                    <p class="truncate">
                                        {{
                                            errorLog.message ||
                                            trans('(no message)')
                                        }}
                                    </p>
                                    <p
                                        v-if="errorLog.file"
                                        class="truncate font-mono text-[11px] text-muted-foreground"
                                    >
                                        {{ errorLog.file }}:{{ errorLog.line }}
                                    </p>
                                </td>
                                <td
                                    class="hidden max-w-72 px-4 py-3 lg:table-cell"
                                >
                                    <ErrorLogRequestSummary
                                        :context="errorLog.context"
                                        :method="errorLog.method"
                                        :url="errorLog.url"
                                    />
                                    <span
                                        class="block truncate text-xs text-muted-foreground/80"
                                    >
                                        {{
                                            errorLog.user_name ?? trans('Guest')
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="hidden px-4 py-3 text-xs text-muted-foreground tabular-nums md:table-cell"
                                >
                                    {{
                                        errorLog.created_at
                                            ? formatDateTime(
                                                  errorLog.created_at,
                                              )
                                            : '—'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="show(errorLog.id)"
                                        :aria-label="trans('View error log')"
                                        class="inline-flex text-muted-foreground hover:text-foreground"
                                        @click.stop
                                    >
                                        <ChevronRight class="size-4" />
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!errorLogs.data.length">
                                <td colspan="5" class="px-4 py-12">
                                    <div
                                        class="flex flex-col items-center gap-2 text-center text-muted-foreground"
                                    >
                                        <CheckCircle2
                                            v-if="isShowingCleanSlate"
                                            class="size-10 text-emerald-600/70 dark:text-emerald-400/70"
                                        />
                                        <Bug
                                            v-else
                                            class="size-10 text-muted-foreground/40"
                                        />
                                        <span class="font-medium">
                                            {{
                                                isShowingCleanSlate
                                                    ? trans('No open errors')
                                                    : trans(
                                                          'No error logs found.',
                                                      )
                                            }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <PaginationControls :paginator="errorLogs" />
            </CardContent>
        </Card>
    </PageContainer>
</template>
