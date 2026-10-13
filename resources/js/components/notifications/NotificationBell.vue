<script setup lang="ts">
import { Link, router, usePage, usePoll } from '@inertiajs/vue3';
import { Bell, CheckCheck } from '@lucide/vue';
import { computed, ref } from 'vue';
import NotificationListItem from '@/components/notifications/NotificationListItem.vue';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Spinner } from '@/components/ui/spinner';
import { useNotificationActions } from '@/composables/useNotificationActions';
import { trans } from '@/lib/translation';
import { index } from '@/routes/notifications';
import type { AppNotification } from '@/types';

const notificationProps = ['unreadNotificationsCount', 'recentNotifications'];
const unreadCountPollingInterval = 60_000;
const maximumDisplayedCount = 99;

const page = usePage();
const isOpen = ref(false);
const isLoading = ref(false);

const unreadCount = computed(() => page.props.unreadNotificationsCount);
const unreadCountLabel = computed(() =>
    unreadCount.value > maximumDisplayedCount
        ? `${maximumDisplayedCount}+`
        : String(unreadCount.value),
);
const recentNotifications = computed(
    () => page.props.recentNotifications?.data ?? [],
);

const { openNotification, markAllAsRead } = useNotificationActions({
    reloadOnly: notificationProps,
});

usePoll(unreadCountPollingInterval, { only: ['unreadNotificationsCount'] });

const loadRecentNotifications = (): void => {
    router.reload({
        only: notificationProps,
        onStart: () => (isLoading.value = true),
        onFinish: () => (isLoading.value = false),
    });
};

const handleOpenChange = (open: boolean): void => {
    isOpen.value = open;

    if (open) {
        loadRecentNotifications();
    }
};

const handleNotificationClick = (notification: AppNotification): void => {
    if (notification.action_url) {
        isOpen.value = false;
    }

    openNotification(notification);
};
</script>

<template>
    <Popover :open="isOpen" @update:open="handleOpenChange">
        <PopoverTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="relative cursor-pointer"
                :aria-label="
                    unreadCount
                        ? trans('Notifications (:count unread)', {
                              count: unreadCount,
                          })
                        : trans('Notifications')
                "
                data-test="notification-bell"
            >
                <Bell class="size-5 opacity-80" />
                <span
                    v-if="unreadCount"
                    class="absolute top-1 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-destructive px-1 text-[10px] leading-none font-semibold text-white tabular-nums"
                >
                    {{ unreadCountLabel }}
                </span>
            </Button>
        </PopoverTrigger>

        <PopoverContent align="end" class="w-80 p-0 sm:w-96">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <span class="text-sm font-semibold">
                    {{ trans('Notifications') }}
                </span>
                <Button
                    v-if="unreadCount"
                    variant="ghost"
                    size="sm"
                    class="h-7 px-2 text-xs"
                    @click="markAllAsRead"
                >
                    <CheckCheck />
                    {{ trans('Mark all as read') }}
                </Button>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div
                    v-if="isLoading && !recentNotifications.length"
                    class="flex justify-center py-10"
                >
                    <Spinner />
                </div>
                <p
                    v-else-if="!recentNotifications.length"
                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                >
                    {{ trans('You have no notifications.') }}
                </p>
                <ul v-else class="divide-y">
                    <li
                        v-for="notification in recentNotifications"
                        :key="notification.id"
                    >
                        <button
                            type="button"
                            class="w-full cursor-pointer px-4 py-3 transition-colors hover:bg-muted/50"
                            :class="{ 'bg-muted/30': !notification.is_read }"
                            @click="handleNotificationClick(notification)"
                        >
                            <NotificationListItem
                                :notification="notification"
                                compact
                            />
                        </button>
                    </li>
                </ul>
            </div>

            <div class="border-t p-2">
                <Button variant="ghost" size="sm" class="w-full" as-child>
                    <Link :href="index()" @click="isOpen = false">
                        {{ trans('View all notifications') }}
                    </Link>
                </Button>
            </div>
        </PopoverContent>
    </Popover>
</template>
