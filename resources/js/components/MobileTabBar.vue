<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Menu } from '@lucide/vue';
import MobileAccountSheet from '@/components/MobileAccountSheet.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { trans } from '@/lib/translation';
import type { NavItem } from '@/types';

withDefaults(
    defineProps<{
        items: NavItem[];
        accountItems?: NavItem[];
    }>(),
    {
        accountItems: () => [],
    },
);

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-40 flex border-t border-border/70 bg-background/85 pb-[env(safe-area-inset-bottom)] backdrop-blur-xl md:hidden"
        :aria-label="trans('Navigation')"
    >
        <Link
            v-for="item in items"
            :key="item.title"
            :href="item.href"
            prefetch
            class="group flex flex-1 flex-col items-center gap-1 pt-2 pb-1 text-[11px] font-medium text-muted-foreground transition-colors active:scale-95"
            :class="{ 'text-foreground': isCurrentOrParentUrl(item.href) }"
        >
            <span
                class="relative flex h-7 w-14 items-center justify-center rounded-full transition-colors duration-300"
                :class="
                    isCurrentOrParentUrl(item.href)
                        ? 'bg-muted'
                        : 'group-active:bg-muted'
                "
            >
                <component :is="item.icon" class="size-5" />
                <span
                    v-if="item.badge"
                    class="absolute top-0 right-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-destructive px-1 text-[10px] leading-none font-semibold text-white"
                >
                    {{ item.badge > 99 ? '99+' : item.badge }}
                </span>
            </span>
            {{ trans(item.title) }}
        </Link>

        <MobileAccountSheet :extra-items="accountItems">
            <template #icon>
                <Menu class="size-5" />
            </template>
        </MobileAccountSheet>
    </nav>
</template>
