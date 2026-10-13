<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, KeyRound, ShieldCheck, Zap } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { trans } from '@/lib/translation';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();
const appName = page.props.name;
const currentYear = new Date().getFullYear();

const highlights = [
    {
        icon: ShieldCheck,
        title: trans('Secure authentication'),
        description: trans(
            'Login, registration, email verification and password reset ready to use.',
        ),
    },
    {
        icon: KeyRound,
        title: trans('Passkeys and 2FA'),
        description: trans(
            'Passwordless sign-in and two-factor authentication to protect every account.',
        ),
    },
    {
        icon: Zap,
        title: trans('Modern stack'),
        description: trans(
            'Laravel, Vue, Inertia and Tailwind working together for a fast experience.',
        ),
    },
];
</script>

<template>
    <div class="grid min-h-svh bg-background lg:grid-cols-2">
        <aside
            class="relative hidden overflow-hidden bg-zinc-950 p-10 text-white lg:flex lg:flex-col"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgb(255_255_255/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.05)_1px,transparent_1px)] [mask-image:radial-gradient(ellipse_at_top_left,black_30%,transparent_75%)] bg-[size:48px_48px]"
            />
            <div
                class="pointer-events-none absolute -top-40 -left-40 size-[520px] rounded-full bg-[var(--chart-1)]/25 blur-3xl"
            />
            <div
                class="pointer-events-none absolute -right-32 -bottom-48 size-[440px] rounded-full bg-[var(--chart-3)]/15 blur-3xl"
            />

            <Link
                :href="home()"
                class="relative flex items-center gap-2 text-lg font-semibold"
            >
                <AppLogoIcon class="size-8 fill-current text-white" />
                {{ appName }}
            </Link>

            <div class="relative mt-auto flex max-w-md flex-col gap-10">
                <div class="flex flex-col gap-4">
                    <h2
                        class="text-4xl font-semibold tracking-tight text-balance"
                    >
                        {{
                            trans('Everything you need to launch your product.')
                        }}
                    </h2>
                    <p class="text-lg text-balance text-zinc-400">
                        {{
                            trans(
                                'A solid foundation with secure authentication and a modern stack, ready from day one.',
                            )
                        }}
                    </p>
                </div>

                <ul class="flex flex-col gap-6">
                    <li
                        v-for="highlight in highlights"
                        :key="highlight.title"
                        class="flex gap-4"
                    >
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5"
                        >
                            <component :is="highlight.icon" class="size-5" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-medium">{{
                                highlight.title
                            }}</span>
                            <span class="text-sm text-zinc-400">{{
                                highlight.description
                            }}</span>
                        </div>
                    </li>
                </ul>
            </div>

            <p class="relative mt-16 text-sm text-zinc-500">
                &copy; {{ currentYear }} {{ appName }}.
                {{ trans('All rights reserved.') }}
            </p>
        </aside>

        <main class="flex flex-col p-6 md:p-10">
            <div class="flex items-center justify-between">
                <Link
                    :href="home()"
                    class="flex items-center gap-2 font-semibold lg:hidden"
                >
                    <AppLogoIcon class="size-7 fill-current" />
                    {{ appName }}
                </Link>
                <Link
                    :href="home()"
                    class="ml-auto flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    {{ trans('Back to home') }}
                </Link>
            </div>

            <div class="flex flex-1 items-center justify-center py-12">
                <div class="flex w-full max-w-sm flex-col gap-8">
                    <div class="flex flex-col gap-2">
                        <h1
                            v-if="title"
                            class="text-2xl font-semibold tracking-tight"
                        >
                            {{ title }}
                        </h1>
                        <p
                            v-if="description"
                            class="text-sm text-muted-foreground"
                        >
                            {{ description }}
                        </p>
                    </div>
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
