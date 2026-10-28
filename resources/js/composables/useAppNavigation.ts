import { usePage } from '@inertiajs/vue3';
import { Bell, Blocks, Bug, LayoutGrid, Users } from '@lucide/vue';
import type { ComputedRef } from 'vue';
import { computed } from 'vue';
import { components, dashboard } from '@/routes';
import { index as errorLogsIndex } from '@/routes/admin/error-logs';
import { index as usersIndex } from '@/routes/admin/users';
import { index as notificationsIndex } from '@/routes/notifications';
import type { NavItem } from '@/types';

export function useMainNavigation(): ComputedRef<NavItem[]> {
    const page = usePage();

    return computed(() => [
        { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
        { title: 'Components', href: components(), icon: Blocks },
        {
            title: 'Notifications',
            href: notificationsIndex(),
            icon: Bell,
            badge: page.props.unreadNotificationsCount,
        },
    ]);
}

export function useAdminNavigation(): ComputedRef<NavItem[]> {
    const page = usePage();

    return computed(() => [
        { title: 'Users', href: usersIndex(), icon: Users },
        {
            title: 'Error logs',
            href: errorLogsIndex(),
            icon: Bug,
            badge: page.props.openErrorLogsCount,
        },
    ]);
}
