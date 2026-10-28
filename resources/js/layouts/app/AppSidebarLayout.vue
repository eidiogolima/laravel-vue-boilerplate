<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ShieldCheck } from '@lucide/vue';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import MobileTabBar from '@/components/MobileTabBar.vue';
import { Toaster } from '@/components/ui/sonner';
import { useMainNavigation } from '@/composables/useAppNavigation';
import { dashboard as adminDashboard } from '@/routes/admin';
import type { BreadcrumbItem, NavItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const mainNavItems = useMainNavigation();

const accountItems: NavItem[] = usePage().props.auth.can.accessAdminPanel
    ? [{ title: 'Admin panel', href: adminDashboard(), icon: ShieldCheck }]
    : [];
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent
            variant="sidebar"
            class="min-w-0 overflow-x-clip pb-[calc(4.5rem+env(safe-area-inset-bottom))] md:pb-0"
        >
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
        <MobileTabBar :items="mainNavItems" :account-items="accountItems" />
        <Toaster />
    </AppShell>
</template>
