<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ShieldCheck } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavAppearance from '@/components/NavAppearance.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useMainNavigation } from '@/composables/useAppNavigation';
import { dashboard } from '@/routes';
import { dashboard as adminDashboard } from '@/routes/admin';
import type { NavItem } from '@/types';

const page = usePage();

const mainNavItems = useMainNavigation();

const administrationNavItems: NavItem[] = [
    {
        title: 'Admin panel',
        href: adminDashboard(),
        icon: ShieldCheck,
    },
];

const canAccessAdminPanel = page.props.auth.can.accessAdminPanel;
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain
                v-if="canAccessAdminPanel"
                label="Administration"
                :items="administrationNavItems"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavAppearance />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
