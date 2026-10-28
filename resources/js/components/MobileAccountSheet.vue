<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronRight, LogOut, Settings } from '@lucide/vue';
import { computed } from 'vue';
import AppearanceSegmentedControl from '@/components/appearance/AppearanceSegmentedControl.vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserInfo from '@/components/UserInfo.vue';
import { trans } from '@/lib/translation';
import { logout } from '@/routes';
import { edit as editProfile } from '@/routes/profile';
import type { NavItem } from '@/types';

withDefaults(
    defineProps<{
        extraItems?: NavItem[];
        isActive?: boolean;
    }>(),
    {
        extraItems: () => [],
        isActive: false,
    },
);

const user = computed(() => usePage().props.auth.user);

const handleLogout = (): void => {
    router.flushAll();
};
</script>

<template>
    <Sheet>
        <SheetTrigger as-child>
            <button
                type="button"
                class="group flex flex-1 flex-col items-center gap-1 pt-2 pb-1 text-[11px] font-medium text-muted-foreground transition-colors active:scale-95"
                :class="{ 'text-foreground': isActive }"
                data-test="mobile-account-button"
            >
                <span
                    class="flex h-7 w-14 items-center justify-center rounded-full transition-colors"
                    :class="isActive ? 'bg-muted' : 'group-active:bg-muted'"
                >
                    <slot name="icon" />
                </span>
                {{ trans('More') }}
            </button>
        </SheetTrigger>
        <SheetContent
            side="bottom"
            class="gap-0 rounded-t-3xl pb-[env(safe-area-inset-bottom)]"
        >
            <div
                class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-muted-foreground/30"
            />
            <SheetHeader class="sr-only">
                <SheetTitle>{{ trans('More') }}</SheetTitle>
                <SheetDescription>{{ user.email }}</SheetDescription>
            </SheetHeader>

            <div class="flex flex-col gap-4 p-4">
                <div
                    class="flex items-center gap-3 rounded-2xl border border-border bg-card p-3"
                >
                    <UserInfo :user="user" :show-email="true" />
                </div>

                <nav class="flex flex-col overflow-hidden rounded-2xl border">
                    <Link
                        v-for="item in extraItems"
                        :key="item.title"
                        :href="item.href"
                        class="flex items-center gap-3 border-b px-4 py-3.5 text-sm font-medium transition-colors active:bg-muted"
                    >
                        <component :is="item.icon" class="size-5" />
                        {{ trans(item.title) }}
                        <ChevronRight
                            class="ml-auto size-4 text-muted-foreground"
                        />
                    </Link>
                    <Link
                        :href="editProfile()"
                        class="flex items-center gap-3 px-4 py-3.5 text-sm font-medium transition-colors active:bg-muted"
                    >
                        <Settings class="size-5" />
                        {{ trans('Settings') }}
                        <ChevronRight
                            class="ml-auto size-4 text-muted-foreground"
                        />
                    </Link>
                </nav>

                <div
                    class="flex items-center gap-3 rounded-2xl border px-4 py-3"
                >
                    <span class="text-sm font-medium">{{
                        trans('Theme')
                    }}</span>
                    <AppearanceSegmentedControl class="ml-auto max-w-44" />
                </div>

                <Link
                    :href="logout()"
                    as="button"
                    class="flex items-center justify-center gap-2 rounded-2xl bg-destructive/10 px-4 py-3.5 text-sm font-medium text-destructive transition-colors active:bg-destructive/20"
                    data-test="logout-button"
                    @click="handleLogout"
                >
                    <LogOut class="size-5" />
                    {{ trans('Log out') }}
                </Link>
            </div>
        </SheetContent>
    </Sheet>
</template>
