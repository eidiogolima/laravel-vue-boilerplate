import { parseDate } from '@internationalized/date';
import type {
    AcquisitionChannel,
    CustomerRevenue,
    DailyRevenue,
    DailySalesSummary,
    DashboardReport,
    OrderStatus,
    SalesSummary,
    SampleOrder,
    SubscriptionPlan,
} from '@/types';

const firstNames = [
    'Ana',
    'Bruno',
    'Camila',
    'Diego',
    'Eduarda',
    'Felipe',
    'Gabriela',
    'Henrique',
    'Isabela',
    'João',
    'Larissa',
    'Marcos',
    'Natália',
    'Otávio',
    'Paula',
    'Rafael',
    'Sofia',
    'Thiago',
    'Vitória',
    'Lucas',
];

const lastNames = [
    'Almeida',
    'Barbosa',
    'Cardoso',
    'Costa',
    'Ferreira',
    'Gomes',
    'Lima',
    'Martins',
    'Oliveira',
    'Pereira',
    'Ribeiro',
    'Rocha',
    'Santos',
    'Souza',
];

const planPrices: Record<SubscriptionPlan, number> = {
    Pro: 49,
    Business: 149,
};

const weekdayDemandFactors = [0.7, 1.05, 1.12, 1.1, 1.14, 0.98, 0.78];

const trendStartDate = parseDate('2026-01-01');

function createSeededRandom(seed: string): () => number {
    let hash = 2166136261;

    for (const character of seed) {
        hash ^= character.charCodeAt(0);
        hash = Math.imul(hash, 16777619);
    }

    let state = hash >>> 0;

    return () => {
        state = (state + 0x6d2b79f5) >>> 0;
        let mixed = state;
        mixed = Math.imul(mixed ^ (mixed >>> 15), mixed | 1);
        mixed ^= mixed + Math.imul(mixed ^ (mixed >>> 7), mixed | 61);

        return ((mixed ^ (mixed >>> 14)) >>> 0) / 4294967296;
    };
}

function pickRandomItem<T>(items: T[], random: () => number): T {
    return items[Math.floor(random() * items.length)];
}

function pickOrderStatus(random: () => number): OrderStatus {
    const roll = random();

    if (roll < 0.86) {
        return 'paid';
    }

    if (roll < 0.92) {
        return 'pending';
    }

    if (roll < 0.96) {
        return 'refunded';
    }

    return 'failed';
}

function pickAcquisitionChannel(random: () => number): AcquisitionChannel {
    const roll = random();

    if (roll < 0.48) {
        return 'organic';
    }

    if (roll < 0.76) {
        return 'referral';
    }

    return 'ads';
}

function removeDiacritics(text: string): string {
    return text.normalize('NFD').replace(/\p{Diacritic}/gu, '');
}

function generateOrdersForDate(isoDate: string): SampleOrder[] {
    const date = parseDate(isoDate);
    const random = createSeededRandom(isoDate);
    const daysSinceTrendStart = date.compare(trendStartDate);
    const growthTrend = 1 + (daysSinceTrendStart / 365) * 0.4;
    const weekdayIndex = new Date(`${isoDate}T12:00:00`).getDay();
    const orderCount = Math.max(
        4,
        Math.round(
            24 *
                growthTrend *
                weekdayDemandFactors[weekdayIndex] *
                (0.8 + random() * 0.4),
        ),
    );

    return Array.from({ length: orderCount }, (_, orderIndex) => {
        const firstName = pickRandomItem(firstNames, random);
        const lastName = pickRandomItem(lastNames, random);
        const plan: SubscriptionPlan = random() < 0.7 ? 'Pro' : 'Business';
        const seats = 1 + Math.floor(random() * 4);
        const hour = String(8 + Math.floor(random() * 14)).padStart(2, '0');
        const minute = String(Math.floor(random() * 60)).padStart(2, '0');
        const emailUser = removeDiacritics(
            `${firstName}.${lastName}`,
        ).toLowerCase();

        return {
            id: `#${String(Math.abs(daysSinceTrendStart) * 100 + orderIndex + 10000)}`,
            customerName: `${firstName} ${lastName}`,
            customerEmail: `${emailUser}@example.com`,
            plan,
            seats,
            channel: pickAcquisitionChannel(random),
            amount: planPrices[plan] * seats,
            status: pickOrderStatus(random),
            isNewCustomer: random() < 0.4,
            orderedAt: `${isoDate}T${hour}:${minute}:00`,
        };
    });
}

function listDatesBetween(startDate: string, endDate: string): string[] {
    const dates: string[] = [];
    const lastDate = parseDate(endDate);
    let currentDate = parseDate(startDate);

    while (currentDate.compare(lastDate) <= 0) {
        dates.push(currentDate.toString());
        currentDate = currentDate.add({ days: 1 });
    }

    return dates;
}

function sumPaidRevenueByDate(
    dates: string[],
    orders: SampleOrder[],
): DailyRevenue[] {
    const revenueByDate = new Map(dates.map((date) => [date, 0]));

    for (const order of orders) {
        if (order.status !== 'paid') {
            continue;
        }

        const orderDate = order.orderedAt.slice(0, 10);
        revenueByDate.set(
            orderDate,
            (revenueByDate.get(orderDate) ?? 0) + order.amount,
        );
    }

    return dates.map((date) => ({
        date,
        revenue: revenueByDate.get(date) ?? 0,
    }));
}

function sortByMostRecent(orders: SampleOrder[]): SampleOrder[] {
    return [...orders].sort((first, second) =>
        second.orderedAt.localeCompare(first.orderedAt),
    );
}

export function buildSampleDashboardReport(
    startDate: string,
    endDate: string,
): DashboardReport {
    const currentDates = listDatesBetween(startDate, endDate);
    const previousEndDate = parseDate(startDate).subtract({ days: 1 });
    const previousStartDate = previousEndDate.subtract({
        days: currentDates.length - 1,
    });
    const previousDates = listDatesBetween(
        previousStartDate.toString(),
        previousEndDate.toString(),
    );
    const currentOrders = currentDates.flatMap(generateOrdersForDate);
    const previousOrders = previousDates.flatMap(generateOrdersForDate);

    return {
        currentOrders: sortByMostRecent(currentOrders),
        previousOrders,
        currentDailyRevenue: sumPaidRevenueByDate(currentDates, currentOrders),
        previousDailyRevenue: sumPaidRevenueByDate(
            previousDates,
            previousOrders,
        ),
    };
}

export function summarizeSales(orders: SampleOrder[]): SalesSummary {
    const paidOrders = orders.filter((order) => order.status === 'paid');
    const revenue = paidOrders.reduce(
        (total, order) => total + order.amount,
        0,
    );

    return {
        revenue,
        paidOrders: paidOrders.length,
        newCustomers: orders.filter((order) => order.isNewCustomer).length,
        averageTicket: paidOrders.length ? revenue / paidOrders.length : 0,
    };
}

export function calculateChangeRatio(
    currentValue: number,
    previousValue: number,
): number | null {
    if (previousValue === 0) {
        return null;
    }

    return (currentValue - previousValue) / previousValue;
}

export function sumPaidRevenueByChannel(
    orders: SampleOrder[],
): Record<AcquisitionChannel, number> {
    const revenueByChannel: Record<AcquisitionChannel, number> = {
        organic: 0,
        referral: 0,
        ads: 0,
    };

    for (const order of orders) {
        if (order.status === 'paid') {
            revenueByChannel[order.channel] += order.amount;
        }
    }

    return revenueByChannel;
}

export function countOrdersByWeekday(orders: SampleOrder[]): number[] {
    const ordersByWeekday = [0, 0, 0, 0, 0, 0, 0];

    for (const order of orders) {
        ordersByWeekday[new Date(order.orderedAt).getDay()] += 1;
    }

    return ordersByWeekday;
}

export function summarizeSalesByDate(
    dates: string[],
    orders: SampleOrder[],
): DailySalesSummary[] {
    const ordersByDate = new Map<string, SampleOrder[]>(
        dates.map((date) => [date, []]),
    );

    for (const order of orders) {
        ordersByDate.get(order.orderedAt.slice(0, 10))?.push(order);
    }

    return dates.map((date) => ({
        date,
        ...summarizeSales(ordersByDate.get(date) ?? []),
    }));
}

export function rankCustomersByRevenue(
    orders: SampleOrder[],
    limit: number,
): CustomerRevenue[] {
    const revenueByCustomer = new Map<string, CustomerRevenue>();

    for (const order of orders) {
        if (order.status !== 'paid') {
            continue;
        }

        const customer = revenueByCustomer.get(order.customerEmail) ?? {
            customerName: order.customerName,
            customerEmail: order.customerEmail,
            revenue: 0,
            paidOrders: 0,
        };
        customer.revenue += order.amount;
        customer.paidOrders += 1;
        revenueByCustomer.set(order.customerEmail, customer);
    }

    return [...revenueByCustomer.values()]
        .sort((first, second) => second.revenue - first.revenue)
        .slice(0, limit);
}
