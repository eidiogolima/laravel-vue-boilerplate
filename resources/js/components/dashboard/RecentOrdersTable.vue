<script setup lang="ts">
import { ChevronLeft, ChevronRight, Download, Search } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import OrderStatusBadge from '@/components/dashboard/OrderStatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatCurrency, formatDateTime, formatNumber } from '@/lib/formatters';
import { trans } from '@/lib/translation';
import type { AcquisitionChannel, OrderStatus, SampleOrder } from '@/types';

type Props = {
    orders: SampleOrder[];
    channelLabels: Record<AcquisitionChannel, string>;
};

const props = defineProps<Props>();

const ordersPerPage = 8;
const allStatusesFilter = 'all';

const statusFilterOptions: {
    value: OrderStatus | typeof allStatusesFilter;
    label: string;
}[] = [
    { value: allStatusesFilter, label: 'All statuses' },
    { value: 'paid', label: 'Paid' },
    { value: 'pending', label: 'Pending' },
    { value: 'refunded', label: 'Refunded' },
    { value: 'failed', label: 'Failed' },
];

const searchTerm = ref('');
const selectedStatus = ref<string>(allStatusesFilter);
const currentPage = ref(1);

const filteredOrders = computed(() => {
    const normalizedSearchTerm = searchTerm.value.trim().toLowerCase();

    return props.orders.filter((order) => {
        const matchesStatus =
            selectedStatus.value === allStatusesFilter ||
            order.status === selectedStatus.value;
        const matchesSearch =
            !normalizedSearchTerm ||
            [order.id, order.customerName, order.customerEmail].some((field) =>
                field.toLowerCase().includes(normalizedSearchTerm),
            );

        return matchesStatus && matchesSearch;
    });
});

const lastPage = computed(() =>
    Math.max(1, Math.ceil(filteredOrders.value.length / ordersPerPage)),
);
const firstVisibleIndex = computed(
    () => (currentPage.value - 1) * ordersPerPage,
);
const visibleOrders = computed(() =>
    filteredOrders.value.slice(
        firstVisibleIndex.value,
        firstVisibleIndex.value + ordersPerPage,
    ),
);
const paginationSummary = computed(() =>
    trans('Showing :from–:to of :total', {
        from: filteredOrders.value.length
            ? formatNumber(firstVisibleIndex.value + 1)
            : 0,
        to: formatNumber(firstVisibleIndex.value + visibleOrders.value.length),
        total: formatNumber(filteredOrders.value.length),
    }),
);

watch([searchTerm, selectedStatus, () => props.orders], () => {
    currentPage.value = 1;
});

const escapeCsvValue = (value: string | number): string =>
    `"${String(value).replaceAll('"', '""')}"`;

const exportFilteredOrdersAsCsv = (): void => {
    const header = [
        trans('Order'),
        trans('Customer'),
        trans('Email'),
        trans('Plan'),
        trans('Channel'),
        trans('Status'),
        trans('Date'),
        trans('Amount'),
    ];
    const rows = filteredOrders.value.map((order) => [
        order.id,
        order.customerName,
        order.customerEmail,
        order.plan,
        props.channelLabels[order.channel],
        trans(
            statusFilterOptions.find((option) => option.value === order.status)
                ?.label ?? order.status,
        ),
        order.orderedAt,
        order.amount.toFixed(2),
    ]);
    const csvContent = [header, ...rows]
        .map((row) => row.map(escapeCsvValue).join(';'))
        .join('\n');
    const downloadLink = document.createElement('a');
    downloadLink.href = URL.createObjectURL(
        new Blob([`﻿${csvContent}`], { type: 'text/csv;charset=utf-8' }),
    );
    downloadLink.download = 'orders-report.csv';
    downloadLink.click();
    URL.revokeObjectURL(downloadLink.href);
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ trans('Orders report') }}</CardTitle>
            <CardDescription>
                {{ trans('Every order in the selected period.') }}
            </CardDescription>
            <CardAction>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!filteredOrders.length"
                    @click="exportFilteredOrdersAsCsv"
                >
                    <Download />
                    {{ trans('Export CSV') }}
                </Button>
            </CardAction>
        </CardHeader>

        <CardContent class="flex flex-col gap-4">
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
                            trans('Search by order, customer or email')
                        "
                        :aria-label="
                            trans('Search by order, customer or email')
                        "
                    />
                </div>
                <Select v-model="selectedStatus">
                    <SelectTrigger
                        class="w-full sm:w-48"
                        :aria-label="trans('Status')"
                    >
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in statusFilterOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ trans(option.label) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-medium">
                                {{ trans('Order') }}
                            </th>
                            <th class="px-4 py-3 font-medium">
                                {{ trans('Customer') }}
                            </th>
                            <th
                                class="hidden px-4 py-3 font-medium md:table-cell"
                            >
                                {{ trans('Plan') }}
                            </th>
                            <th
                                class="hidden px-4 py-3 font-medium lg:table-cell"
                            >
                                {{ trans('Channel') }}
                            </th>
                            <th class="px-4 py-3 font-medium">
                                {{ trans('Status') }}
                            </th>
                            <th
                                class="hidden px-4 py-3 font-medium sm:table-cell"
                            >
                                {{ trans('Date') }}
                            </th>
                            <th class="px-4 py-3 text-right font-medium">
                                {{ trans('Amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="order in visibleOrders"
                            :key="`${order.id}-${order.orderedAt}`"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <td
                                class="px-4 py-3 font-mono text-xs text-muted-foreground"
                            >
                                {{ order.id }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{
                                        order.customerName
                                    }}</span>
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ order.customerEmail }}</span
                                    >
                                </div>
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                {{ order.plan }}
                                <span class="text-muted-foreground">
                                    × {{ order.seats }}
                                </span>
                            </td>
                            <td
                                class="hidden px-4 py-3 text-muted-foreground lg:table-cell"
                            >
                                {{ channelLabels[order.channel] }}
                            </td>
                            <td class="px-4 py-3">
                                <OrderStatusBadge :status="order.status" />
                            </td>
                            <td
                                class="hidden px-4 py-3 text-muted-foreground sm:table-cell"
                            >
                                {{ formatDateTime(order.orderedAt) }}
                            </td>
                            <td
                                class="px-4 py-3 text-right font-medium tabular-nums"
                            >
                                {{ formatCurrency(order.amount) }}
                            </td>
                        </tr>
                        <tr v-if="!visibleOrders.length">
                            <td
                                colspan="7"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                {{ trans('No orders found.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col items-center justify-between gap-3 text-sm text-muted-foreground sm:flex-row"
            >
                <span class="tabular-nums">{{ paginationSummary }}</span>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="icon-sm"
                        :disabled="currentPage <= 1"
                        :aria-label="trans('Previous page')"
                        @click="currentPage -= 1"
                    >
                        <ChevronLeft />
                    </Button>
                    <span class="tabular-nums">
                        {{
                            trans('Page :current of :last', {
                                current: currentPage,
                                last: lastPage,
                            })
                        }}
                    </span>
                    <Button
                        variant="outline"
                        size="icon-sm"
                        :disabled="currentPage >= lastPage"
                        :aria-label="trans('Next page')"
                        @click="currentPage += 1"
                    >
                        <ChevronRight />
                    </Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
