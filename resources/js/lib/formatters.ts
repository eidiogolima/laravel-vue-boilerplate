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

export function formatPreciseDateTime(isoDateTime: string): string {
    return new Intl.DateTimeFormat(currentLanguageTag(), {
        dateStyle: 'short',
        timeStyle: 'medium',
    }).format(new Date(isoDateTime));
}

export function formatShortWeekday(weekdayIndex: number): string {
    const referenceSunday = new Date('2026-01-04T12:00:00');
    referenceSunday.setDate(referenceSunday.getDate() + weekdayIndex);

    return new Intl.DateTimeFormat(currentLanguageTag(), {
        weekday: 'short',
    }).format(referenceSunday);
}

const relativeTimeUnits: [Intl.RelativeTimeFormatUnit, number][] = [
    ['year', 60 * 60 * 24 * 365],
    ['month', 60 * 60 * 24 * 30],
    ['week', 60 * 60 * 24 * 7],
    ['day', 60 * 60 * 24],
    ['hour', 60 * 60],
    ['minute', 60],
];

export function formatRelativeTime(isoDateTime: string): string {
    const elapsedSeconds = Math.round(
        (new Date(isoDateTime).getTime() - Date.now()) / 1000,
    );
    const formatter = new Intl.RelativeTimeFormat(currentLanguageTag(), {
        numeric: 'auto',
    });
    const [unit, secondsInUnit] = relativeTimeUnits.find(
        ([, secondsInUnit]) => Math.abs(elapsedSeconds) >= secondsInUnit,
    ) ?? ['second', 1];

    return formatter.format(Math.round(elapsedSeconds / secondsInUnit), unit);
}
