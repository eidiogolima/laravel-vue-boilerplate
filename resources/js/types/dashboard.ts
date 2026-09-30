export type OrderStatus = 'paid' | 'pending' | 'refunded' | 'failed';

export type AcquisitionChannel = 'organic' | 'referral' | 'ads';

export type SubscriptionPlan = 'Pro' | 'Business';

export type SampleOrder = {
    id: string;
    customerName: string;
    customerEmail: string;
    plan: SubscriptionPlan;
    seats: number;
    channel: AcquisitionChannel;
    amount: number;
    status: OrderStatus;
    isNewCustomer: boolean;
    orderedAt: string;
};

export type DailyRevenue = {
    date: string;
    revenue: number;
};

export type SalesSummary = {
    revenue: number;
    paidOrders: number;
    newCustomers: number;
    averageTicket: number;
};

export type DashboardReport = {
    currentOrders: SampleOrder[];
    previousOrders: SampleOrder[];
    currentDailyRevenue: DailyRevenue[];
    previousDailyRevenue: DailyRevenue[];
};

export type DailySalesSummary = SalesSummary & {
    date: string;
};

export type CustomerRevenue = {
    customerName: string;
    customerEmail: string;
    revenue: number;
    paidOrders: number;
};
