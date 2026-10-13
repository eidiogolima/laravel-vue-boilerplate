<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Clock,
    Compass,
    House,
    LayoutGrid,
    Lock,
    RotateCw,
    ServerCrash,
    Wrench,
} from '@lucide/vue';
import type { LucideIcon } from '@lucide/vue';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { trans } from '@/lib/translation';
import { dashboard, home } from '@/routes';

type ErrorDetails = {
    icon: LucideIcon;
    title: string;
    description: string;
    canRetry: boolean;
};

const { status } = defineProps<{
    status: number;
}>();

const errorDetailsByStatus: Record<number, ErrorDetails> = {
    403: {
        icon: Lock,
        title: 'Access denied',
        description:
            "You don't have permission to access this page. If you think this is a mistake, contact the administrator.",
        canRetry: false,
    },
    404: {
        icon: Compass,
        title: 'Page not found',
        description:
            "Sorry, we couldn't find the page you're looking for. It may have been moved or no longer exists.",
        canRetry: false,
    },
    429: {
        icon: Clock,
        title: 'Too many requests',
        description:
            "You're making requests too quickly. Please wait a moment and try again.",
        canRetry: true,
    },
    500: {
        icon: ServerCrash,
        title: 'Something went wrong',
        description:
            'An unexpected error occurred on our side. Please try again in a few moments.',
        canRetry: true,
    },
    503: {
        icon: Wrench,
        title: 'Under maintenance',
        description:
            "We're making some improvements right now. We'll be back shortly.",
        canRetry: true,
    },
};

const page = usePage();
const appName = page.props.name;
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const details = computed(
    () => errorDetailsByStatus[status] ?? errorDetailsByStatus[500],
);

const goBack = (): void => {
    window.history.back();
};

const reload = (): void => {
    window.location.reload();
};
</script>

<template>
    <Head :title="trans(details.title)" />

    <div
        class="relative isolate flex min-h-svh flex-col overflow-hidden bg-background text-foreground"
    >
        <div
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 -z-10"
        >
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,var(--border)_1px,transparent_1px),linear-gradient(to_bottom,var(--border)_1px,transparent_1px)] mask-[radial-gradient(ellipse_60%_50%_at_50%_40%,black_30%,transparent_100%)] bg-size-[48px_48px] opacity-60"
            />
            <div
                class="absolute top-1/4 left-1/2 size-[36rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary/10 blur-3xl dark:bg-primary/15"
            />
        </div>

        <header class="flex items-center justify-center px-6 py-8">
            <Link
                :href="home()"
                class="flex items-center gap-2 rounded-md text-sm font-semibold outline-none focus-visible:ring-2 focus-visible:ring-ring"
            >
                <AppLogoIcon class="size-7 fill-current" />
                <span>{{ appName }}</span>
            </Link>
        </header>

        <main
            class="flex flex-1 flex-col items-center justify-center px-6 pb-24 text-center"
        >
            <div
                class="mb-8 flex size-16 items-center justify-center rounded-2xl border bg-card shadow-sm"
            >
                <component
                    :is="details.icon"
                    class="size-7 text-muted-foreground"
                    aria-hidden="true"
                />
            </div>

            <p
                class="bg-linear-to-b from-foreground to-foreground/30 bg-clip-text text-8xl leading-none font-bold tracking-tighter text-transparent tabular-nums select-none sm:text-9xl"
                aria-hidden="true"
            >
                {{ status }}
            </p>

            <h1 class="mt-6 text-2xl font-semibold tracking-tight sm:text-3xl">
                {{ trans(details.title) }}
            </h1>
            <p
                class="mt-3 max-w-md text-base text-balance text-muted-foreground"
            >
                {{ trans(details.description) }}
            </p>

            <div
                class="mt-10 flex w-full max-w-xs flex-col gap-3 sm:w-auto sm:max-w-none sm:flex-row"
            >
                <Button
                    v-if="details.canRetry"
                    size="lg"
                    data-test="error-retry-button"
                    @click="reload"
                >
                    <RotateCw />
                    {{ trans('Try again') }}
                </Button>
                <Button v-else size="lg" as-child data-test="error-home-button">
                    <Link :href="isAuthenticated ? dashboard() : home()">
                        <component :is="isAuthenticated ? LayoutGrid : House" />
                        {{
                            isAuthenticated
                                ? trans('Go to dashboard')
                                : trans('Go to home page')
                        }}
                    </Link>
                </Button>
                <Button size="lg" variant="outline" @click="goBack">
                    <ArrowLeft />
                    {{ trans('Go back') }}
                </Button>
            </div>
        </main>

        <footer class="px-6 py-6 text-center text-xs text-muted-foreground">
            {{ trans('Error code') }}: {{ status }}
        </footer>
    </div>
</template>
