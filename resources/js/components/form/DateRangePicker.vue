<script setup lang="ts">
import type { DateValue } from '@internationalized/date';
import {
    endOfMonth,
    getLocalTimeZone,
    parseDate,
    startOfMonth,
    today,
} from '@internationalized/date';
import { CalendarRange } from '@lucide/vue';
import type { DateRange } from 'reka-ui';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { currentLanguageTag, trans } from '@/lib/translation';
import { cn } from '@/lib/utils';

export type DateRangeValue = {
    start: string | null;
    end: string | null;
};

type DateRangePreset = {
    label: string;
    resolveRange: () => { start: DateValue; end: DateValue };
};

type Props = {
    id: string;
    label: string;
    startName?: string;
    endName?: string;
    placeholder?: string;
    description?: string;
    error?: string;
    minDate?: string;
    maxDate?: string;
    disabled?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    disabled: false,
});

const model = defineModel<DateRangeValue>({
    default: () => ({ start: null, end: null }),
});

const isOpen = ref(false);
const languageTag = currentLanguageTag();
const timeZone = getLocalTimeZone();
const dateFormatter = new Intl.DateTimeFormat(languageTag, {
    dateStyle: 'medium',
});

const presets: DateRangePreset[] = [
    {
        label: trans('Last 7 days'),
        resolveRange: () => ({
            start: today(timeZone).subtract({ days: 6 }),
            end: today(timeZone),
        }),
    },
    {
        label: trans('Last 30 days'),
        resolveRange: () => ({
            start: today(timeZone).subtract({ days: 29 }),
            end: today(timeZone),
        }),
    },
    {
        label: trans('This month'),
        resolveRange: () => ({
            start: startOfMonth(today(timeZone)),
            end: endOfMonth(today(timeZone)),
        }),
    },
];

const selectedRange = computed<DateRange>(() => ({
    start: model.value.start ? parseDate(model.value.start) : undefined,
    end: model.value.end ? parseDate(model.value.end) : undefined,
}));
const minimumDate = computed(() =>
    props.minDate ? parseDate(props.minDate) : undefined,
);
const maximumDate = computed(() =>
    props.maxDate ? parseDate(props.maxDate) : undefined,
);
const formattedRange = computed(() => {
    const { start, end } = selectedRange.value;

    if (start && end) {
        return dateFormatter.formatRange(
            start.toDate(timeZone),
            end.toDate(timeZone),
        );
    }

    if (start) {
        return dateFormatter.format(start.toDate(timeZone));
    }

    return null;
});

const selectRange = (range: DateRange): void => {
    model.value = {
        start: range.start ? range.start.toString() : null,
        end: range.end ? range.end.toString() : null,
    };

    if (range.start && range.end) {
        isOpen.value = false;
    }
};

const applyPreset = (preset: DateRangePreset): void => {
    selectRange(preset.resolveRange());
};

const clearRange = (): void => {
    model.value = { start: null, end: null };
};
</script>

<template>
    <div class="grid gap-2">
        <Label :for="id">{{ label }}</Label>
        <Popover v-model:open="isOpen">
            <PopoverTrigger as-child>
                <Button
                    :id="id"
                    type="button"
                    variant="outline"
                    :disabled="disabled"
                    :aria-invalid="Boolean(error)"
                    :class="
                        cn(
                            'w-full justify-start font-normal',
                            !formattedRange && 'text-muted-foreground',
                        )
                    "
                >
                    <CalendarRange />
                    {{
                        formattedRange ??
                        placeholder ??
                        trans('Pick a date range')
                    }}
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" align="start">
                <div class="flex flex-col sm:flex-row">
                    <div
                        class="flex flex-row flex-wrap gap-1 border-b p-3 sm:flex-col sm:border-r sm:border-b-0"
                    >
                        <Button
                            v-for="preset in presets"
                            :key="preset.label"
                            type="button"
                            variant="ghost"
                            size="sm"
                            class="justify-start"
                            @click="applyPreset(preset)"
                        >
                            {{ preset.label }}
                        </Button>
                    </div>
                    <RangeCalendar
                        :model-value="selectedRange"
                        :locale="languageTag"
                        :min-value="minimumDate"
                        :max-value="maximumDate"
                        :number-of-months="2"
                        initial-focus
                        @update:model-value="selectRange"
                    />
                </div>
                <div class="flex justify-end border-t p-3">
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="clearRange"
                    >
                        {{ trans('Clear') }}
                    </Button>
                </div>
            </PopoverContent>
        </Popover>
        <input
            v-if="startName"
            type="hidden"
            :name="startName"
            :value="model.start ?? ''"
        />
        <input
            v-if="endName"
            type="hidden"
            :name="endName"
            :value="model.end ?? ''"
        />
        <p v-if="description" class="text-xs text-muted-foreground">
            {{ description }}
        </p>
        <InputError :message="error" />
    </div>
</template>
