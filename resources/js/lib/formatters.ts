import { currentLanguageTag } from '@/lib/translation';

const currencyCode = 'BRL';

export function formatCurrency(value: number): string {
    return new Intl.NumberFormat(currentLanguageTag(), {
        style: 'currency',
        currency: currencyCode,
    }).format(value);
}

export function formatCompactCurrency(value: number): string {
    return new Intl.NumberFormat(currentLanguageTag(), {
        style: 'currency',
        currency: currencyCode,
        notation: 'compact',
        maximumFractionDigits: 1,
    }).format(value);
}

export function formatNumber(value: number): string {
    return new Intl.NumberFormat(currentLanguageTag()).format(value);
}

export function formatSignedPercent(ratio: number): string {
    return new Intl.NumberFormat(currentLanguageTag(), {
        style: 'percent',
        maximumFractionDigits: 1,
        signDisplay: 'exceptZero',
    }).format(ratio);
}

export function formatPercent(ratio: number): string {
    return new Intl.NumberFormat(currentLanguageTag(), {
        style: 'percent',
        maximumFractionDigits: 1,
    }).format(ratio);
}

export function formatDayAndMonth(isoDate: string): string {
    return new Intl.DateTimeFormat(currentLanguageTag(), {
        day: '2-digit',
        month: 'short',
    }).format(new Date(`${isoDate}T12:00:00`));
}

export function formatLongDate(isoDate: string): string {
    return new Intl.DateTimeFormat(currentLanguageTag(), {
        dateStyle: 'medium',
    }).format(new Date(`${isoDate}T12:00:00`));
}

export function formatDateTime(isoDateTime: string): string {
    return new Intl.DateTimeFormat(currentLanguageTag(), {
        dateStyle: 'short',
        timeStyle: 'short',
    }).format(new Date(isoDateTime));
}

export function formatShortWeekday(weekdayIndex: number): string {
    const referenceSunday = new Date('2026-01-04T12:00:00');
    referenceSunday.setDate(referenceSunday.getDate() + weekdayIndex);

    return new Intl.DateTimeFormat(currentLanguageTag(), {
        weekday: 'short',
    }).format(referenceSunday);
}
