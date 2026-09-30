<script setup lang="ts">
import { getLocalTimeZone, today } from '@internationalized/date';
import { Head } from '@inertiajs/vue3';
import { Receipt, ShoppingCart, UserPlus, Wallet } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import AreaChart from '@/components/charts/AreaChart.vue';
import BarChart from '@/components/charts/BarChart.vue';
import DonutChart from '@/components/charts/DonutChart.vue';
import RecentOrdersTable from '@/components/dashboard/RecentOrdersTable.vue';
import StatTile from '@/components/dashboard/StatTile.vue';
import type { DateRangeValue } from '@/components/form/DateRangePicker.vue';
import DateRangePicker from '@/components/form/DateRangePicker.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { getInitials } from '@/composables/useInitials';
import {
    formatCompactCurrency,
    formatCurrency,
    formatDayAndMonth,
    formatLongDate,
    formatNumber,
    formatShortWeekday,
} from '@/lib/formatters';
import {
    buildSampleDashboardReport,
    calculateChangeRatio,
    countOrdersByWeekday,
    rankCustomersByRevenue,
    sumPaidRevenueByChannel,
    summarizeSales,
    summarizeSalesByDate,
} from '@/lib/sampleDashboardData';
import { trans } from '@/lib/translation';
import { dashboard } from '@/routes';
import type { AcquisitionChannel } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const todayDate = today(getLocalTimeZone());
const defaultPeriod: DateRangeValue = {
    start: todayDate.subtract({ days: 29 }).toString(),
    end: todayDate.toString(),
};

const selectedPeriod = ref<DateRangeValue>({ ...defaultPeriod });
const reportPeriod = ref({
    start: defaultPeriod.start as string,
    end: defaultPeriod.end as string,
});

watch(selectedPeriod, ({ start, end }) => {
    if (start && end) {
        reportPeriod.value = { start, end };
    }
});

const channelLabels = computed<Record<AcquisitionChannel, string>>(() => ({
    organic: trans('Organic'),
    referral: trans('Referral'),
    ads: trans('Paid ads'),
}));

const report = computed(() =>
    buildSampleDashboardReport(
        reportPeriod.value.start,
        reportPeriod.value.end,
    ),
);
const currentSummary = computed(() =>
    summarizeSales(report.value.currentOrders),
);
const previousSummary = computed(() =>
    summarizeSales(report.value.previousOrders),
);
const dailySummaries = computed(() =>
    summarizeSalesByDate(
        report.value.currentDailyRevenue.map((day) => day.date),
        report.value.currentOrders,
    ),
);

const statTiles = computed(() => [
    {
        label: trans('Revenue'),
        value: formatCurrency(currentSummary.value.revenue),
        icon: Wallet,
        changeRatio: calculateChangeRatio(
            currentSummary.value.revenue,
            previousSummary.value.revenue,
        ),
        trendValues: dailySummaries.value.map((day) => day.revenue),
    },
    {
        label: trans('Paid orders'),
        value: formatNumber(currentSummary.value.paidOrders),
        icon: ShoppingCart,
        changeRatio: calculateChangeRatio(
            currentSummary.value.paidOrders,
            previousSummary.value.paidOrders,
        ),
        trendValues: dailySummaries.value.map((day) => day.paidOrders),
    },
    {
        label: trans('New customers'),
        value: formatNumber(currentSummary.value.newCustomers),
        icon: UserPlus,
        changeRatio: calculateChangeRatio(
            currentSummary.value.newCustomers,
            previousSummary.value.newCustomers,
        ),
        trendValues: dailySummaries.value.map((day) => day.newCustomers),
    },
    {
        label: trans('Average ticket'),
        value: formatCurrency(currentSummary.value.averageTicket),
        icon: Receipt,
        changeRatio: calculateChangeRatio(
            currentSummary.value.averageTicket,
            previousSummary.value.averageTicket,
        ),
        trendValues: dailySummaries.value.map((day) => day.averageTicket),
    },
]);

const revenuePoints = computed(() =>
    report.value.currentDailyRevenue.map((day, dayIndex) => ({
        label: formatDayAndMonth(day.date),
        value: day.revenue,
        comparisonValue: report.value.previousDailyRevenue[dayIndex]?.revenue,
    })),
);

const channelSegments = computed(() => {
    const revenueByChannel = sumPaidRevenueByChannel(
        report.value.currentOrders,
    );

    return (Object.keys(revenueByChannel) as AcquisitionChannel[]).map(
        (channel) => ({
            label: channelLabels.value[channel],
            value: revenueByChannel[channel],
        }),
    );
});

const weekdayItems = computed(() =>
    countOrdersByWeekday(report.value.currentOrders).map(
        (orderCount, weekdayIndex) => ({
            label: formatShortWeekday(weekdayIndex).replace('.', ''),
            value: orderCount,
        }),
    ),
);

const topCustomers = computed(() =>
    rankCustomersByRevenue(report.value.currentOrders, 5),
);
const topCustomerRevenue = computed(() => topCustomers.value[0]?.revenue ?? 1);

const periodDescription = computed(() =>
    trans(':start to :end', {
        start: formatLongDate(reportPeriod.value.start),
        end: formatLongDate(reportPeriod.value.end),
    }),
);
</script>

<template>
    <Head :title="trans('Dashboard')" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between"
        >
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ trans('Dashboard') }}
                    </h1>
                    <Badge variant="outline">{{ trans('Sample data') }}</Badge>
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ trans('Overview of your business performance.') }}
                </p>
            </div>
            <div class="w-full md:w-80">
                <DateRangePicker
                    id="dashboard_period"
                    v-model="selectedPeriod"
                    :label="trans('Period')"
                    :max-date="todayDate.toString()"
                />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatTile
                v-for="statTile in statTiles"
                :key="statTile.label"
                :label="statTile.label"
                :value="statTile.value"
                :icon="statTile.icon"
                :change-ratio="statTile.changeRatio"
                :trend-values="statTile.trendValues"
            />
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            <Card class="xl:col-span-2">
                <CardHeader>
                    <CardTitle>{{ trans('Revenue over time') }}</CardTitle>
                    <CardDescription>{{ periodDescription }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <AreaChart
                        :points="revenuePoints"
                        :series-label="trans('Current period')"
                        :comparison-label="trans('Previous period')"
                        :value-formatter="formatCurrency"
                        :axis-formatter="formatCompactCurrency"
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ trans('Revenue by channel') }}</CardTitle>
                    <CardDescription>
                        {{ trans('Where your paying customers come from.') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-1 items-center">
                    <DonutChart
                        :segments="channelSegments"
                        :total-label="trans('Total revenue')"
                        :value-formatter="formatCompactCurrency"
                    />
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            <Card class="xl:col-span-2">
                <CardHeader>
                    <CardTitle>{{ trans('Orders by weekday') }}</CardTitle>
                    <CardDescription>
                        {{ trans('Busiest days in the selected period.') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <BarChart
                        :items="weekdayItems"
                        :series-label="trans('Orders')"
                        :value-formatter="formatNumber"
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ trans('Top customers') }}</CardTitle>
                    <CardDescription>
                        {{ trans('Highest paid revenue in the period.') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <ul class="flex flex-col gap-4">
                        <li
                            v-for="customer in topCustomers"
                            :key="customer.customerEmail"
                            class="flex flex-col gap-2"
                        >
                            <div class="flex items-center gap-3">
                                <Avatar class="size-8">
                                    <AvatarFallback class="text-xs">
                                        {{ getInitials(customer.customerName) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex min-w-0 flex-1 flex-col">
                                    <span class="truncate text-sm font-medium">
                                        {{ customer.customerName }}
                                    </span>
                                    <span
                                        class="truncate text-xs text-muted-foreground"
                                    >
                                        {{
                                            trans(':count paid orders', {
                                                count: customer.paidOrders,
                                            })
                                        }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium tabular-nums">
                                    {{ formatCurrency(customer.revenue) }}
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-chart-1"
                                    :style="{
                                        width: `${(customer.revenue / topCustomerRevenue) * 100}%`,
                                    }"
                                />
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>

        <RecentOrdersTable
            :orders="report.currentOrders"
            :channel-labels="channelLabels"
        />
    </div>
</template>
