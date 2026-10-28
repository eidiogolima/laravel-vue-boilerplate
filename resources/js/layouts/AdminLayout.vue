<script setup lang="ts">
import { ArrowLeft } from '@lucide/vue';
import AdminSidebar from '@/components/AdminSidebar.vue';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import MobileTabBar from '@/components/MobileTabBar.vue';
import { Toaster } from '@/components/ui/sonner';
import { useAdminNavigation } from '@/composables/useAppNavigation';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const administrationNavItems = useAdminNavigation();

const accountItems: NavItem[] = [
    { title: 'Back to app', href: dashboard(), icon: ArrowLeft },
];
</script>

<template>
    <AppShell variant="sidebar">
        <AdminSidebar />
        <AppContent
            variant="sidebar"
            class="min-w-0 overflow-x-clip pb-[calc(4.5rem+env(safe-area-inset-bottom))] md:pb-0"
        >
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
        <MobileTabBar
            :items="administrationNavItems"
            :account-items="accountItems"
        />
        <Toaster />
    </AppShell>
</template>
