<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import CheckboxField from '@/components/form/CheckboxField.vue';
import DatePicker from '@/components/form/DatePicker.vue';
import type { DateRangeValue } from '@/components/form/DateRangePicker.vue';
import DateRangePicker from '@/components/form/DateRangePicker.vue';
import LoadingButton from '@/components/form/LoadingButton.vue';
import type { SelectOption } from '@/components/form/SelectField.vue';
import SelectField from '@/components/form/SelectField.vue';
import TextField from '@/components/form/TextField.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { currentLanguageTag, trans } from '@/lib/translation';
import { components } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Components',
                href: components(),
            },
        ],
    },
});

const planOptions: SelectOption[] = [
    { value: 'starter', label: trans('Starter') },
    { value: 'pro', label: trans('Pro') },
    { value: 'business', label: trans('Business') },
];

const fullName = ref('');
const selectedPlan = ref<string>();
const acceptsNewsletter = ref(true);
const startDate = ref<string | null>(null);
const reportPeriod = ref<DateRangeValue>({ start: null, end: null });
const isSaving = ref(false);

const simulateSaving = (): void => {
    isSaving.value = true;
    setTimeout(() => {
        isSaving.value = false;
    }, 1500);
};
</script>

<template>
    <Head :title="trans('Components')" />

    <div class="flex flex-col gap-10 p-4 md:p-6">
        <Heading
            :title="trans('Components')"
            :description="
                trans(
                    'Reusable components available to build the authenticated area.',
                )
            "
        />

        <section class="flex flex-col gap-4 rounded-xl border p-6">
            <Heading variant="small" :title="trans('Buttons')" />
            <div class="flex flex-wrap items-center gap-3">
                <Button>{{ trans('Primary') }}</Button>
                <Button variant="secondary">{{ trans('Secondary') }}</Button>
                <Button variant="outline">{{ trans('Outline') }}</Button>
                <Button variant="ghost">{{ trans('Ghost') }}</Button>
                <Button variant="link">{{ trans('Link') }}</Button>
                <Button variant="destructive">
                    <Trash2 />{{ trans('Delete') }}
                </Button>
                <Button size="icon" :aria-label="trans('Add')">
                    <Plus />
                </Button>
                <LoadingButton
                    type="button"
                    :loading="isSaving"
                    @click="simulateSaving"
                >
                    {{ isSaving ? trans('Saving...') : trans('Save') }}
                </LoadingButton>
            </div>
        </section>

        <section class="flex flex-col gap-6 rounded-xl border p-6">
            <Heading
                variant="small"
                :title="trans('Form fields')"
                :description="
                    trans(
                        'Fields with label, description and validation error ready to use.',
                    )
                "
            />
            <div class="grid gap-6 md:grid-cols-2">
                <TextField
                    id="full_name"
                    v-model="fullName"
                    :label="trans('Full name')"
                    :placeholder="trans('Full name')"
                    :description="trans('As it appears on your documents.')"
                />
                <TextField
                    id="email_example"
                    type="email"
                    :label="trans('Email address')"
                    :placeholder="trans('email@example.com')"
                    :error="
                        trans('The email field must be a valid email address.')
                    "
                />
                <SelectField
                    id="plan"
                    v-model="selectedPlan"
                    :label="trans('Plan')"
                    :options="planOptions"
                />
                <DatePicker
                    id="start_date"
                    v-model="startDate"
                    name="start_date"
                    :label="trans('Start date')"
                />
                <DateRangePicker
                    id="report_period"
                    v-model="reportPeriod"
                    start-name="report_period_start"
                    end-name="report_period_end"
                    :label="trans('Report period')"
                />
                <CheckboxField
                    id="newsletter"
                    v-model="acceptsNewsletter"
                    :label="trans('Receive news by email')"
                    :description="trans('You can unsubscribe at any time.')"
                />
            </div>
            <dl
                class="grid gap-2 rounded-lg bg-muted p-4 text-sm sm:grid-cols-2"
            >
                <div>
                    <dt class="text-muted-foreground">{{ trans('Name') }}</dt>
                    <dd>{{ fullName || '—' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">{{ trans('Plan') }}</dt>
                    <dd>{{ selectedPlan ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">
                        {{ trans('Start date') }}
                    </dt>
                    <dd>{{ startDate ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">
                        {{ trans('Report period') }}
                    </dt>
                    <dd>
                        {{ reportPeriod.start ?? '—' }} →
                        {{ reportPeriod.end ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">
                        {{ trans('Receive news by email') }}
                    </dt>
                    <dd>
                        {{ acceptsNewsletter ? trans('Yes') : trans('No') }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="flex flex-col gap-4 rounded-xl border p-6">
            <Heading variant="small" :title="trans('Calendar')" />
            <div class="flex flex-wrap items-start gap-6">
                <Calendar
                    class="w-fit rounded-lg border"
                    :locale="currentLanguageTag()"
                />
                <RangeCalendar
                    class="w-fit rounded-lg border"
                    :locale="currentLanguageTag()"
                    :number-of-months="2"
                />
            </div>
        </section>
    </div>
</template>
