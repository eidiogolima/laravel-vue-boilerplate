<script setup lang="ts">
import { Head, Link, router, setLayoutProps, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Check, CheckCircle2, Copy, RotateCcw } from '@lucide/vue';
import { useClipboard } from '@vueuse/core';
import { computed, ref } from 'vue';
import PageContainer from '@/components/PageContainer.vue';
import DeleteErrorLogDialog from '@/components/admin/error-logs/DeleteErrorLogDialog.vue';
import ErrorLogRequestSummary from '@/components/admin/error-logs/ErrorLogRequestSummary.vue';
import StatusCodeBadge from '@/components/admin/error-logs/StatusCodeBadge.vue';
import PaginationControls from '@/components/admin/PaginationControls.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { getInitials } from '@/composables/useInitials';
import { formatNumber, formatPreciseDateTime } from '@/lib/formatters';
import { trans } from '@/lib/translation';
import { index, resolution, show } from '@/routes/admin/error-logs';
import type {
    ErrorLogDetail,
    ErrorLogListItem,
    ErrorLogOccurrenceSummary,
    PaginatedResource,
} from '@/types';

type DetailTab = 'trace' | 'request' | 'payload' | 'headers' | 'occurrences';

const props = defineProps<{
    errorLog: ErrorLogDetail;
    occurrences: PaginatedResource<ErrorLogListItem>;
    occurrenceSummary: ErrorLogOccurrenceSummary;
}>();

setLayoutProps({
    breadcrumbs: [
        { title: 'Error logs', href: index() },
        { title: 'Error details', href: show(props.errorLog.id) },
    ],
});

const formatOptionalDateTime = (isoDateTime: string | null): string =>
    isoDateTime ? formatPreciseDateTime(isoDateTime) : '—';

const activeTab = ref<DetailTab>(
    new URL(usePage().url, 'http://localhost').searchParams.has(
        'occurrences_page',
    )
        ? 'occurrences'
        : 'trace',
);

const detailTabs = computed(() => [
    {
        value: 'trace' as const,
        text: trans('Stack trace'),
        count: props.errorLog.trace.length,
    },
    { value: 'request' as const, text: trans('Request'), count: null },
    {
        value: 'payload' as const,
        text: trans('Payload'),
        count: Object.keys(props.errorLog.payload).length,
    },
    {
        value: 'headers' as const,
        text: trans('Headers'),
        count: Object.keys(props.errorLog.headers).length,
    },
    {
        value: 'occurrences' as const,
        text: trans('Occurrences'),
        count: props.occurrenceSummary.total,
    },
]);

const isShowingVendorFrames = ref(false);

const visibleFrames = computed(() =>
    props.errorLog.trace
        .map((frame, frameIndex) => ({ ...frame, frameIndex }))
        .filter((frame) => isShowingVendorFrames.value || !frame.is_vendor),
);

const vendorFramesCount = computed(
    () => props.errorLog.trace.filter((frame) => frame.is_vendor).length,
);

const requestDetails = computed(() => [
    {
        text: trans('Context'),
        value:
            props.errorLog.context === 'http'
                ? 'HTTP'
                : trans('Console / queue'),
        isMonospace: false,
    },
    { text: trans('Method'), value: props.errorLog.method, isMonospace: true },
    { text: 'URL', value: props.errorLog.url, isMonospace: true },
    { text: trans('Route'), value: props.errorLog.route, isMonospace: true },
    { text: 'IP', value: props.errorLog.ip, isMonospace: true },
    {
        text: trans('User agent'),
        value: props.errorLog.user_agent,
        isMonospace: false,
    },
    {
        text: trans('Environment'),
        value: props.errorLog.environment,
        isMonospace: false,
    },
    {
        text: trans('Recorded at'),
        value: formatOptionalDateTime(props.errorLog.created_at),
        isMonospace: false,
    },
]);

const keyValueTabContent = computed((): Record<string, unknown> =>
    activeTab.value === 'payload'
        ? props.errorLog.payload
        : props.errorLog.headers,
);

const { copy, copied } = useClipboard({ legacy: true });

const copyErrorReport = (): void => {
    const { errorLog } = props;
    const stackTrace = errorLog.trace
        .map(
            (frame, frameIndex) =>
                `#${frameIndex} ${frame.file ?? '[internal]'}${frame.line ? `(${frame.line})` : ''}: ${frame.function}()`,
        )
        .join('\n');

    copy(
        [
            `[${errorLog.status_code}] ${errorLog.exception}: ${errorLog.message ?? ''}`,
            `${errorLog.file}:${errorLog.line}`,
            errorLog.method ? `${errorLog.method} ${errorLog.url}` : null,
            '',
            stackTrace,
        ]
            .filter((reportLine) => reportLine !== null)
            .join('\n'),
    );
};

const isUpdatingResolution = ref(false);

const toggleResolution = (): void => {
    router.patch(
        resolution.url(props.errorLog.id),
        { resolved: props.errorLog.resolved_at === null },
        {
            preserveScroll: true,
            onStart: () => (isUpdatingResolution.value = true),
            onFinish: () => (isUpdatingResolution.value = false),
        },
    );
};
</script>

<template>
    <Head :title="trans('Error #:id', { id: errorLog.id })" />

    <PageContainer>
        <Link
            :href="index()"
            class="inline-flex w-fit items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"
        >
            <ArrowLeft class="size-4" />
            {{ trans('Back to error logs') }}
        </Link>

        <Card>
            <CardContent class="flex flex-col gap-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <StatusCodeBadge
                                :status-code="errorLog.status_code"
                            />
                            <span
                                class="font-mono text-sm font-semibold break-all"
                            >
                                {{ errorLog.exception }}
                            </span>
                            <Badge
                                v-if="errorLog.resolved_at"
                                variant="secondary"
                            >
                                <CheckCircle2 />
                                {{
                                    trans('Resolved at :date', {
                                        date: formatOptionalDateTime(
                                            errorLog.resolved_at,
                                        ),
                                    })
                                }}
                            </Badge>
                            <Badge v-else variant="destructive">
                                {{ trans('Unresolved') }}
                            </Badge>
                        </div>

                        <p
                            class="mt-3 text-base font-medium break-words whitespace-pre-wrap"
                        >
                            {{ errorLog.message || trans('(no message)') }}
                        </p>

                        <p
                            v-if="errorLog.file"
                            class="mt-2 font-mono text-xs break-all text-muted-foreground"
                        >
                            {{ errorLog.file
                            }}<span class="text-foreground"
                                >:{{ errorLog.line }}</span
                            >
                        </p>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            @click="copyErrorReport"
                        >
                            <Check v-if="copied" />
                            <Copy v-else />
                            {{ copied ? trans('Copied') : trans('Copy error') }}
                        </Button>
                        <Button
                            :variant="
                                errorLog.resolved_at ? 'outline' : 'default'
                            "
                            size="sm"
                            :disabled="isUpdatingResolution"
                            data-test="toggle-resolution-button"
                            @click="toggleResolution"
                        >
                            <RotateCcw v-if="errorLog.resolved_at" />
                            <CheckCircle2 v-else />
                            {{
                                errorLog.resolved_at
                                    ? trans('Reopen')
                                    : trans('Mark as resolved')
                            }}
                        </Button>
                        <DeleteErrorLogDialog :error-log-id="errorLog.id" />
                    </div>
                </div>

                <dl
                    class="grid gap-4 border-t pt-4 text-sm sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div class="min-w-0">
                        <dt class="text-xs text-muted-foreground">
                            {{ trans('Request') }}
                        </dt>
                        <dd class="mt-1">
                            <ErrorLogRequestSummary
                                :context="errorLog.context"
                                :method="errorLog.method"
                                :url="errorLog.route ?? errorLog.url"
                            />
                        </dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-xs text-muted-foreground">
                            {{ trans('User') }}
                        </dt>
                        <dd class="mt-1 flex min-w-0 items-center gap-2">
                            <template v-if="errorLog.user">
                                <Avatar class="size-6 rounded-md">
                                    <AvatarImage
                                        v-if="errorLog.user.avatar"
                                        :src="errorLog.user.avatar"
                                        :alt="errorLog.user.name"
                                    />
                                    <AvatarFallback
                                        class="rounded-md text-[10px]"
                                    >
                                        {{ getInitials(errorLog.user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <span class="min-w-0">
                                    <span
                                        class="block truncate text-xs font-medium"
                                    >
                                        {{ errorLog.user.name }}
                                    </span>
                                    <span
                                        class="block truncate text-[11px] text-muted-foreground"
                                    >
                                        {{ errorLog.user.email }}
                                    </span>
                                </span>
                            </template>
                            <span v-else class="text-xs text-muted-foreground">
                                {{ trans('Guest (not signed in)') }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">
                            {{ trans('Occurrences') }}
                        </dt>
                        <dd class="mt-1 text-xs">
                            <span class="font-semibold tabular-nums">
                                {{ formatNumber(occurrenceSummary.total) }}×
                            </span>
                            <span class="text-muted-foreground">
                                ·
                                {{
                                    trans('first on :date', {
                                        date: formatOptionalDateTime(
                                            occurrenceSummary.first_occurred_at,
                                        ),
                                    })
                                }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">
                            {{ trans('Recorded at') }}
                        </dt>
                        <dd class="mt-1 text-xs tabular-nums">
                            {{ formatOptionalDateTime(errorLog.created_at) }}
                            <span class="text-muted-foreground">
                                · {{ errorLog.environment }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>

        <div
            v-if="errorLog.code_snippet.length"
            class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-950"
        >
            <div
                class="flex items-center justify-between gap-4 border-b border-zinc-800 px-4 py-2 font-mono text-xs text-zinc-400"
            >
                <span class="truncate">{{ errorLog.file }}</span>
                <span class="shrink-0">
                    {{ trans('line :line', { line: errorLog.line ?? '' }) }}
                </span>
            </div>
            <div class="overflow-x-auto py-2 font-mono text-xs leading-6">
                <div
                    v-for="snippetLine in errorLog.code_snippet"
                    :key="snippetLine.line"
                    class="flex min-w-fit"
                    :class="
                        snippetLine.line === errorLog.line
                            ? 'bg-red-500/15 text-red-100'
                            : 'text-zinc-300'
                    "
                >
                    <span
                        class="w-14 shrink-0 border-l-2 pr-4 text-right select-none"
                        :class="
                            snippetLine.line === errorLog.line
                                ? 'border-red-500 text-red-300'
                                : 'border-transparent text-zinc-600'
                        "
                    >
                        {{ snippetLine.line }}
                    </span>
                    <pre class="pr-4 whitespace-pre">{{
                        snippetLine.code || ' '
                    }}</pre>
                </div>
            </div>
        </div>

        <div
            class="flex w-full gap-1 overflow-x-auto rounded-lg bg-muted p-1 sm:w-fit"
            role="tablist"
        >
            <button
                v-for="tab in detailTabs"
                :key="tab.value"
                type="button"
                role="tab"
                :aria-selected="activeTab === tab.value"
                class="inline-flex shrink-0 items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                :class="
                    activeTab === tab.value
                        ? 'bg-background text-foreground shadow-xs'
                        : 'text-muted-foreground hover:text-foreground'
                "
                @click="activeTab = tab.value"
            >
                {{ tab.text }}
                <span
                    v-if="tab.count !== null"
                    class="text-xs tabular-nums opacity-70"
                >
                    {{ formatNumber(tab.count) }}
                </span>
            </button>
        </div>

        <div v-if="activeTab === 'trace'" class="flex flex-col gap-3">
            <div v-if="vendorFramesCount > 0" class="flex items-center gap-2">
                <Checkbox
                    id="show-vendor-frames"
                    v-model="isShowingVendorFrames"
                />
                <Label
                    for="show-vendor-frames"
                    class="text-xs font-normal text-muted-foreground"
                >
                    {{
                        trans('Show vendor frames (:count)', {
                            count: vendorFramesCount,
                        })
                    }}
                </Label>
            </div>

            <div class="overflow-hidden rounded-lg border">
                <ul class="divide-y">
                    <li
                        v-for="frame in visibleFrames"
                        :key="frame.frameIndex"
                        class="flex items-start gap-3 px-4 py-2.5 font-mono"
                        :class="{ 'opacity-60': frame.is_vendor }"
                    >
                        <span
                            class="w-8 shrink-0 pt-0.5 text-right text-[11px] text-muted-foreground tabular-nums"
                        >
                            #{{ frame.frameIndex }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium break-all">
                                {{ frame.function }}()
                            </p>
                            <p
                                class="text-[11px] break-all text-muted-foreground"
                            >
                                {{ frame.file ?? '[internal]'
                                }}<template v-if="frame.line"
                                    >:{{ frame.line }}</template
                                >
                            </p>
                        </div>
                        <Badge
                            v-if="!frame.is_vendor"
                            variant="outline"
                            class="font-sans"
                        >
                            app
                        </Badge>
                    </li>
                    <li
                        v-if="!visibleFrames.length"
                        class="px-4 py-10 text-center text-sm text-muted-foreground"
                    >
                        {{
                            trans(
                                'No application frames. Show vendor frames to see the full trace.',
                            )
                        }}
                    </li>
                </ul>
            </div>
        </div>

        <div
            v-else-if="activeTab === 'request'"
            class="overflow-hidden rounded-lg border"
        >
            <dl class="divide-y">
                <div
                    v-for="detail in requestDetails"
                    :key="detail.text"
                    class="flex items-start gap-4 px-4 py-2.5 text-xs"
                >
                    <dt class="w-28 shrink-0 text-muted-foreground">
                        {{ detail.text }}
                    </dt>
                    <dd
                        class="min-w-0 flex-1 break-all"
                        :class="{ 'font-mono': detail.isMonospace }"
                    >
                        {{ detail.value ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div
            v-else-if="activeTab === 'payload' || activeTab === 'headers'"
            class="flex flex-col gap-3"
        >
            <div
                v-if="!Object.keys(keyValueTabContent).length"
                class="rounded-lg border px-4 py-10 text-center text-sm text-muted-foreground"
            >
                {{
                    activeTab === 'payload'
                        ? trans('The request did not send any data.')
                        : trans('No headers were recorded.')
                }}
            </div>
            <div
                v-else
                class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-950 p-4"
            >
                <pre class="font-mono text-xs leading-5 text-zinc-200">{{
                    JSON.stringify(keyValueTabContent, null, 2)
                }}</pre>
            </div>
            <p class="text-[11px] text-muted-foreground">
                {{
                    trans(
                        'Sensitive fields (passwords, tokens, cards, cookies) are stored as [redacted].',
                    )
                }}
            </p>
        </div>

        <div v-else class="flex flex-col gap-3">
            <p class="text-xs text-muted-foreground">
                {{
                    trans(
                        ':count occurrence(s) of the same error (same exception, file and line). Last one on :date.',
                        {
                            count: formatNumber(occurrenceSummary.total),
                            date: formatOptionalDateTime(
                                occurrenceSummary.last_occurred_at,
                            ),
                        },
                    )
                }}
            </p>

            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                        <tr
                            v-for="occurrence in occurrences.data"
                            :key="occurrence.id"
                            class="cursor-pointer transition-colors hover:bg-muted/40"
                            @click="router.visit(show(occurrence.id))"
                        >
                            <td class="w-16 px-4 py-2.5">
                                <StatusCodeBadge
                                    :status-code="occurrence.status_code"
                                />
                            </td>
                            <td class="max-w-0 px-4 py-2.5">
                                <ErrorLogRequestSummary
                                    :context="occurrence.context"
                                    :method="occurrence.method"
                                    :url="occurrence.url"
                                />
                            </td>
                            <td
                                class="hidden w-44 truncate px-4 py-2.5 text-xs text-muted-foreground md:table-cell"
                            >
                                {{ occurrence.user_name ?? trans('Guest') }}
                            </td>
                            <td
                                class="w-44 px-4 py-2.5 text-right text-xs text-muted-foreground tabular-nums"
                            >
                                {{
                                    formatOptionalDateTime(
                                        occurrence.created_at,
                                    )
                                }}
                            </td>
                        </tr>
                        <tr v-if="!occurrences.data.length">
                            <td
                                colspan="4"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                {{ trans('This error only happened once.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationControls
                v-if="occurrences.meta.last_page > 1"
                :paginator="occurrences"
            />
        </div>
    </PageContainer>
</template>
