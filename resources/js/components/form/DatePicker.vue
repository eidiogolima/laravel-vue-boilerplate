<script setup lang="ts">
import type { DateValue } from '@internationalized/date';
import { getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { CalendarDays } from '@lucide/vue';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { currentLanguageTag, trans } from '@/lib/translation';

type Props = {
    id: string;
    label: string;
    name?: string;
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

const model = defineModel<string | null>({ default: null });

const isOpen = ref(false);
const languageTag = currentLanguageTag();
const dateFormatter = new Intl.DateTimeFormat(languageTag, {
    dateStyle: 'long',
});

const selectedDate = computed<DateValue | undefined>(() =>
    model.value ? parseDate(model.value) : undefined,
);
const minimumDate = computed(() =>
    props.minDate ? parseDate(props.minDate) : undefined,
);
const maximumDate = computed(() =>
    props.maxDate ? parseDate(props.maxDate) : undefined,
);
const formattedDate = computed(() =>
    selectedDate.value
        ? dateFormatter.format(selectedDate.value.toDate(getLocalTimeZone()))
        : null,
);

const selectDate = (date: DateValue | DateValue[] | undefined): void => {
    if (Array.isArray(date)) {
        return;
    }

    model.value = date ? date.toString() : null;
    isOpen.value = false;
};

const selectToday = (): void => {
    selectDate(today(getLocalTimeZone()));
};

const clearDate = (): void => {
    selectDate(undefined);
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
                            !formattedDate && 'text-muted-foreground',
                        )
                    "
                >
                    <CalendarDays />
                    {{ formattedDate ?? placeholder ?? trans('Pick a date') }}
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" align="start">
                <Calendar
                    :model-value="selectedDate"
                    :locale="languageTag"
                    :min-value="minimumDate"
                    :max-value="maximumDate"
                    initial-focus
                    @update:model-value="selectDate"
                />
                <div class="flex justify-between gap-2 border-t p-3">
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="clearDate"
                    >
                        {{ trans('Clear') }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="selectToday"
                    >
                        {{ trans('Today') }}
                    </Button>
                </div>
            </PopoverContent>
        </Popover>
        <input v-if="name" type="hidden" :name="name" :value="model ?? ''" />
        <p v-if="description" class="text-xs text-muted-foreground">
            {{ description }}
        </p>
        <InputError :message="error" />
    </div>
</template>
