<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Bug, Users } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavAppearance from '@/components/NavAppearance.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Badge } from '@/components/ui/badge';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { trans } from '@/lib/translation';
import { dashboard } from '@/routes';
import { dashboard as adminDashboard } from '@/routes/admin';
import { index as errorLogsIndex } from '@/routes/admin/error-logs';
import { index as usersIndex } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const page = usePage();

const administrationNavItems = computed((): NavItem[] => [
    {
        title: 'Users',
        href: usersIndex(),
        icon: Users,
    },
    {
        title: 'Error logs',
        href: errorLogsIndex(),
        icon: Bug,
        badge: page.props.openErrorLogsCount,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="adminDashboard()">
                            <AppLogo />
                            <Badge
                                variant="outline"
                                class="ml-auto group-data-[collapsible=icon]:hidden"
                            >
                                {{ trans('Admin') }}
                            </Badge>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain label="Administration" :items="administrationNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child :tooltip="trans('Back to app')">
                        <Link :href="dashboard()">
                            <ArrowLeft />
                            <span>{{ trans('Back to app') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <NavAppearance />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
