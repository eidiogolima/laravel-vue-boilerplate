<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Check, Languages } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { trans } from '@/lib/translation';
import { update } from '@/routes/locale';

const page = usePage();

const currentLocale = computed(() => page.props.locale);
const locales = computed(() => page.props.locales);
const currentLocaleLabel = computed(
    () =>
        locales.value.find((locale) => locale.value === currentLocale.value)
            ?.label ?? currentLocale.value,
);

const changeLocale = (locale: string): void => {
    if (locale === currentLocale.value) {
        return;
    }

    router.post(update(), { locale });
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="sm"
                class="gap-2"
                :aria-label="trans('Language')"
                data-test="locale-switcher"
            >
                <Languages class="size-4" />
                <span class="hidden sm:inline">{{ currentLocaleLabel }}</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="min-w-44">
            <DropdownMenuItem
                v-for="locale in locales"
                :key="locale.value"
                class="cursor-pointer justify-between"
                :data-test="`locale-option-${locale.value}`"
                @select="changeLocale(locale.value)"
            >
                {{ locale.label }}
                <Check v-if="locale.value === currentLocale" class="size-4" />
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
