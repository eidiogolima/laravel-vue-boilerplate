<script setup lang="ts">
import { trans } from '@/lib/translation';
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="trans('Appearance settings')" />

    <h1 class="sr-only">{{ trans('Appearance settings') }}</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            :title="trans('Appearance settings')"
            :description="
                trans('Update the appearance settings for your account')
            "
        />
        <AppearanceTabs />
    </div>
</template>
