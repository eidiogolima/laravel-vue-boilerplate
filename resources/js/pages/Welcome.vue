<script setup lang="ts">
import { Head, Link, usePage } from "@inertiajs/vue3";
import {
    ArrowRight,
    Check,
    ChevronDown,
    Code,
    FlaskConical,
    KeyRound,
    Languages,
    ShieldCheck,
    Zap,
} from "@lucide/vue";
import { computed } from "vue";
import AppLogoIcon from "@/components/AppLogoIcon.vue";
import LocaleSwitcher from "@/components/LocaleSwitcher.vue";
import PlaceholderPattern from "@/components/PlaceholderPattern.vue";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { vReveal } from "@/directives/reveal";
import { formatWholeCurrency } from "@/lib/formatters";
import { trans } from "@/lib/translation";
import { dashboard, home, login, register } from "@/routes";

const page = usePage();
const appName = page.props.name;
const isAuthenticated = computed(() => Boolean(page.props.auth.user));
const currentYear = new Date().getFullYear();

const navigationLinks = [
    { href: "#features", label: trans("Features") },
    { href: "#pricing", label: trans("Pricing") },
    { href: "#faq", label: trans("FAQ") },
];

const features = [
    {
        icon: ShieldCheck,
        title: trans("Secure authentication"),
        description: trans(
            "Login, registration, email verification and password reset ready to use.",
        ),
    },
    {
        icon: KeyRound,
        title: trans("Passkeys and 2FA"),
        description: trans(
            "Passwordless sign-in and two-factor authentication to protect every account.",
        ),
    },
    {
        icon: Zap,
        title: trans("Modern stack"),
        description: trans(
            "Laravel, Vue, Inertia and Tailwind working together for a fast experience.",
        ),
    },
    {
        icon: Languages,
        title: trans("Multilingual"),
        description: trans(
            "Interface, emails and validation messages available in English, Spanish and Brazilian Portuguese.",
        ),
    },
    {
        icon: Code,
        title: trans("Clean architecture"),
        description: trans(
            "Single-purpose Actions, strict typing and conventions that make maintenance easy.",
        ),
    },
    {
        icon: FlaskConical,
        title: trans("Tested from day one"),
        description: trans(
            "Feature and unit tests with a CI pipeline that checks every change.",
        ),
    },
];

const planPricesByLocale: Record<
    string,
    { currency: string; pro: number; business: number }
> = {
    en: { currency: "USD", pro: 9, business: 29 },
    es: { currency: "EUR", pro: 9, business: 27 },
    pt_BR: { currency: "BRL", pro: 49, business: 149 },
};

const plans = computed(() => {
    const { currency, pro, business } =
        planPricesByLocale[page.props.locale] ?? planPricesByLocale.en;

    return [
        {
            title: trans("Starter"),
            price: trans("Free"),
            period: "",
            description: trans("For those who want to try the product."),
            benefits: [
                trans("1 user"),
                trans("Core features"),
                trans("Community support"),
            ],
            callToAction: trans("Start for free"),
            isHighlighted: false,
        },
        {
            title: trans("Pro"),
            price: formatWholeCurrency(pro, currency),
            period: trans("/month"),
            description: trans("For growing teams that need more."),
            benefits: [
                trans("Up to 10 users"),
                trans("All features"),
                trans("Email support"),
            ],
            callToAction: trans("Choose Pro"),
            isHighlighted: true,
        },
        {
            title: trans("Business"),
            price: formatWholeCurrency(business, currency),
            period: trans("/month"),
            description: trans("For companies operating at scale."),
            benefits: [
                trans("Unlimited users"),
                trans("All features"),
                trans("Priority support"),
            ],
            callToAction: trans("Choose Business"),
            isHighlighted: false,
        },
    ];
});

const frequentlyAskedQuestions = [
    {
        title: trans("Can I try it before paying?"),
        description: trans(
            "Yes. The Starter plan is free and you can upgrade whenever you want.",
        ),
    },
    {
        title: trans("Can I cancel at any time?"),
        description: trans(
            "Yes. There is no commitment, and you keep access until the end of the paid period.",
        ),
    },
    {
        title: trans("Is my data secure?"),
        description: trans(
            "Yes. We use two-factor authentication, passkeys and encrypted passwords to protect your account.",
        ),
    },
    {
        title: trans("Do you offer support?"),
        description: trans(
            "Yes. Every plan includes support, with faster response times on paid plans.",
        ),
    },
];
</script>

<template>
    <Head :title="trans('Welcome')" />

    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <header
            class="sticky top-0 z-50 border-b border-border/60 bg-background/70 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between gap-6 px-6"
            >
                <Link
                    :href="home()"
                    class="flex items-center gap-2 font-semibold"
                >
                    <AppLogoIcon class="size-7 fill-current" />
                    {{ appName }}
                </Link>

                <nav class="hidden items-center gap-8 text-sm md:flex">
                    <a
                        v-for="navigationLink in navigationLinks"
                        :key="navigationLink.href"
                        :href="navigationLink.href"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                    >
                        {{ navigationLink.label }}
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <LocaleSwitcher />
                    <Button v-if="isAuthenticated" as-child>
                        <Link :href="dashboard()">{{
                            trans("Dashboard")
                        }}</Link>
                    </Button>
                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="login()">{{ trans("Log in") }}</Link>
                        </Button>
                        <Button as-child>
                            <Link :href="register()">{{
                                trans("Get started")
                            }}</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <section class="relative overflow-hidden">
                <div
                    class="landing-grid-background absolute inset-x-0 top-0 -z-10 h-[640px] opacity-60"
                />
                <div
                    class="landing-float absolute top-10 left-1/2 -z-10 h-72 w-[36rem] -translate-x-1/2 rounded-full bg-gradient-to-r from-violet-500/20 via-sky-500/20 to-emerald-500/10 blur-3xl"
                />
                <div
                    class="mx-auto flex max-w-6xl flex-col items-center gap-8 px-6 pt-20 pb-16 text-center md:pt-28"
                >
                    <Badge
                        variant="outline"
                        class="landing-hero-item gap-2 bg-background/60 px-3 py-1 backdrop-blur"
                    >
                        <span class="relative flex size-2">
                            <span
                                class="landing-pulse-ring absolute inset-0 rounded-full bg-green-500"
                            />
                            <span
                                class="relative size-2 rounded-full bg-green-500"
                            />
                        </span>
                        {{ trans("Your SaaS ready to launch") }}
                    </Badge>

                    <h1
                        class="landing-hero-item max-w-3xl text-4xl font-semibold tracking-tight text-balance md:text-6xl lg:text-7xl"
                        style="--landing-delay: 100ms"
                    >
                        <span class="landing-gradient-text">{{
                            trans("Launch your SaaS faster")
                        }}</span>
                    </h1>

                    <p
                        class="landing-hero-item max-w-2xl text-lg text-balance text-muted-foreground"
                        style="--landing-delay: 200ms"
                    >
                        {{
                            trans(
                                "Everything you need to start: authentication, security, settings and a solid architecture, so you can focus on your product.",
                            )
                        }}
                    </p>

                    <div
                        class="landing-hero-item flex flex-col gap-3 sm:flex-row"
                        style="--landing-delay: 300ms"
                    >
                        <Button
                            size="lg"
                            class="group shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/25"
                            as-child
                        >
                            <Link
                                :href="
                                    isAuthenticated ? dashboard() : register()
                                "
                            >
                                {{
                                    isAuthenticated
                                        ? trans("Go to dashboard")
                                        : trans("Start for free")
                                }}
                                <ArrowRight
                                    class="transition-transform group-hover:translate-x-1"
                                />
                            </Link>
                        </Button>
                        <Button
                            size="lg"
                            variant="outline"
                            class="bg-background/60 backdrop-blur transition-all hover:-translate-y-0.5"
                            as-child
                        >
                            <a href="#features">{{ trans("See features") }}</a>
                        </Button>
                    </div>

                    <div
                        class="landing-hero-item relative mt-8 w-full max-w-5xl"
                        style="--landing-delay: 450ms"
                    >
                        <div
                            class="absolute -inset-x-4 -bottom-6 -z-10 h-1/2 rounded-full bg-gradient-to-r from-violet-500/20 via-sky-500/20 to-emerald-500/10 blur-3xl"
                        />
                        <div
                            class="rounded-2xl border border-border bg-card/80 p-2 shadow-2xl shadow-black/10 backdrop-blur"
                        >
                            <div class="flex items-center gap-1.5 px-3 py-2">
                                <span
                                    class="size-2.5 rounded-full bg-red-400/70"
                                />
                                <span
                                    class="size-2.5 rounded-full bg-amber-400/70"
                                />
                                <span
                                    class="size-2.5 rounded-full bg-green-400/70"
                                />
                            </div>
                            <div
                                class="grid gap-2 rounded-xl border border-border bg-background p-4 md:grid-cols-[180px_1fr]"
                            >
                                <div
                                    class="relative hidden h-72 overflow-hidden rounded-md border border-border md:block"
                                >
                                    <PlaceholderPattern />
                                </div>
                                <div class="grid gap-2">
                                    <div class="grid grid-cols-3 gap-2">
                                        <div
                                            v-for="index in 3"
                                            :key="index"
                                            class="relative h-20 overflow-hidden rounded-md border border-border transition-colors hover:border-foreground/30"
                                        >
                                            <PlaceholderPattern />
                                        </div>
                                    </div>
                                    <div
                                        class="relative h-48 overflow-hidden rounded-md border border-border"
                                    >
                                        <PlaceholderPattern />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="scroll-mt-16 border-t border-border">
                <div class="mx-auto flex max-w-6xl flex-col gap-12 px-6 py-24">
                    <div
                        v-reveal
                        class="flex flex-col items-center gap-3 text-center"
                    >
                        <h2
                            class="text-3xl font-semibold tracking-tight md:text-4xl"
                        >
                            {{ trans("Everything you need to get started") }}
                        </h2>
                        <p class="max-w-2xl text-muted-foreground">
                            {{
                                trans(
                                    "A complete foundation so you do not have to rebuild the basics in every project.",
                                )
                            }}
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(feature, index) in features"
                            :key="feature.title"
                            v-reveal="(index % 3) * 100"
                            class="group flex flex-col gap-3 rounded-xl border border-border bg-card p-6 transition-all duration-300 hover:-translate-y-1 hover:border-foreground/20 hover:shadow-xl hover:shadow-black/5"
                        >
                            <div
                                class="flex size-10 items-center justify-center rounded-lg bg-muted transition-colors duration-300 group-hover:bg-primary group-hover:text-primary-foreground"
                            >
                                <component :is="feature.icon" class="size-5" />
                            </div>
                            <h3 class="font-semibold">{{ feature.title }}</h3>
                            <p class="text-sm text-muted-foreground">
                                {{ feature.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="pricing"
                class="scroll-mt-16 border-t border-border bg-muted/40"
            >
                <div class="mx-auto flex max-w-6xl flex-col gap-12 px-6 py-24">
                    <div
                        v-reveal
                        class="flex flex-col items-center gap-3 text-center"
                    >
                        <h2
                            class="text-3xl font-semibold tracking-tight md:text-4xl"
                        >
                            {{ trans("Simple and transparent pricing") }}
                        </h2>
                        <p class="max-w-2xl text-muted-foreground">
                            {{
                                trans(
                                    "Choose the plan that fits your moment. Change whenever you want.",
                                )
                            }}
                        </p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-3">
                        <div
                            v-for="(plan, index) in plans"
                            :key="plan.title"
                            v-reveal="index * 100"
                            :class="[
                                'relative flex flex-col gap-6 rounded-xl border bg-card p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl',
                                plan.isHighlighted
                                    ? 'border-primary shadow-xl lg:scale-105'
                                    : 'border-border',
                            ]"
                        >
                            <Badge
                                v-if="plan.isHighlighted"
                                class="absolute -top-3 left-8"
                            >
                                {{ trans("Most popular") }}
                            </Badge>

                            <div class="flex flex-col gap-2">
                                <h3 class="text-lg font-semibold">
                                    {{ plan.title }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{ plan.description }}
                                </p>
                            </div>

                            <p class="flex items-baseline gap-1">
                                <span class="text-4xl font-semibold">{{
                                    plan.price
                                }}</span>
                                <span class="text-sm text-muted-foreground">{{
                                    plan.period
                                }}</span>
                            </p>

                            <ul class="flex flex-1 flex-col gap-3 text-sm">
                                <li
                                    v-for="benefit in plan.benefits"
                                    :key="benefit"
                                    class="flex items-center gap-2"
                                >
                                    <Check class="size-4 text-green-600" />
                                    {{ benefit }}
                                </li>
                            </ul>

                            <Button
                                :variant="
                                    plan.isHighlighted ? 'default' : 'outline'
                                "
                                as-child
                            >
                                <Link :href="register()">{{
                                    plan.callToAction
                                }}</Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </section>

            <section id="faq" class="scroll-mt-16 border-t border-border">
                <div class="mx-auto flex max-w-3xl flex-col gap-12 px-6 py-24">
                    <h2
                        v-reveal
                        class="text-center text-3xl font-semibold tracking-tight md:text-4xl"
                    >
                        {{ trans("Frequently asked questions") }}
                    </h2>

                    <div
                        v-reveal="100"
                        class="divide-y divide-border overflow-hidden rounded-xl border border-border bg-card"
                    >
                        <details
                            v-for="question in frequentlyAskedQuestions"
                            :key="question.title"
                            class="group p-6 transition-colors open:bg-muted/40 hover:bg-muted/40"
                        >
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between gap-4 font-medium"
                            >
                                {{ question.title }}
                                <ChevronDown
                                    class="size-4 shrink-0 transition-transform group-open:rotate-180"
                                />
                            </summary>
                            <p class="mt-3 text-sm text-muted-foreground">
                                {{ question.description }}
                            </p>
                        </details>
                    </div>
                </div>
            </section>

            <section class="border-t border-border">
                <div class="mx-auto max-w-6xl px-6 py-24">
                    <div
                        v-reveal
                        class="relative flex flex-col items-center gap-6 overflow-hidden rounded-3xl border border-border bg-muted/40 px-6 py-16 text-center"
                    >
                        <div
                            class="landing-grid-background absolute inset-0 -z-10 opacity-50"
                        />
                        <div
                            class="absolute -bottom-24 left-1/2 -z-10 h-48 w-96 -translate-x-1/2 rounded-full bg-gradient-to-r from-violet-500/25 via-sky-500/25 to-emerald-500/15 blur-3xl"
                        />
                        <h2
                            class="text-3xl font-semibold tracking-tight md:text-4xl"
                        >
                            {{ trans("Ready to get started?") }}
                        </h2>
                        <p class="max-w-xl text-muted-foreground">
                            {{
                                trans(
                                    "Create your account in less than a minute.",
                                )
                            }}
                        </p>
                        <Button
                            size="lg"
                            class="group transition-all hover:-translate-y-0.5"
                            as-child
                        >
                            <Link
                                :href="
                                    isAuthenticated ? dashboard() : register()
                                "
                            >
                                {{
                                    isAuthenticated
                                        ? trans("Go to dashboard")
                                        : trans("Create my account")
                                }}
                                <ArrowRight
                                    class="transition-transform group-hover:translate-x-1"
                                />
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-border">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm text-muted-foreground md:flex-row"
            >
                <div class="flex items-center gap-2">
                    <AppLogoIcon class="size-5 fill-current" />
                    <span>© {{ currentYear }} {{ appName }}.</span>
                    <span>{{ trans("All rights reserved.") }}</span>
                </div>
                <nav class="flex gap-6">
                    <a
                        v-for="navigationLink in navigationLinks"
                        :key="navigationLink.href"
                        :href="navigationLink.href"
                        class="transition-colors hover:text-foreground"
                    >
                        {{ navigationLink.label }}
                    </a>
                </nav>
            </div>
        </footer>
    </div>
</template>
