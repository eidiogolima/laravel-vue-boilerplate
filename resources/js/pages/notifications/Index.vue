<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BellOff,
    Check,
    CheckCheck,
    ChevronLeft,
    ChevronRight,
    ExternalLink,
    Trash2,
} from '@lucide/vue';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import PageContainer from '@/components/PageContainer.vue';
import NotificationListItem from '@/components/notifications/NotificationListItem.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useNotificationActions } from '@/composables/useNotificationActions';
import { formatNumber } from '@/lib/formatters';
import { trans } from '@/lib/translation';
import { index } from '@/routes/notifications';
import type {
    AppNotification,
    NotificationFilter,
    PaginatedResource,
} from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Notifications',
                href: index(),
            },
        ],
    },
});

defineProps<{
    notifications: PaginatedResource<AppNotification>;
    filter: NotificationFilter;
}>();

const filterOptions: { value: NotificationFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'unread', label: 'Unread' },
];

const page = usePage();
const unreadCount = computed(() => page.props.unreadNotificationsCount);

const { markAsRead, openNotification, markAllAsRead, deleteNotification } =
    useNotificationActions();
</script>

<template>
    <Head :title="trans('Notifications')" />

    <PageContainer>
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                :title="trans('Notifications')"
                :description="
                    trans('System alerts and updates about your account.')
                "
            />
            <Button
                variant="outline"
                :disabled="!unreadCount"
                @click="markAllAsRead"
            >
                <CheckCheck />
                {{ trans('Mark all as read') }}
            </Button>
        </div>

        <Card>
            <CardContent class="flex flex-col gap-4">
                <div class="flex gap-2" role="tablist">
                    <Button
                        v-for="option in filterOptions"
                        :key="option.value"
                        :variant="filter === option.value ? 'default' : 'ghost'"
                        size="sm"
                        role="tab"
                        :aria-selected="filter === option.value"
                        as-child
                    >
                        <Link
                            :href="
                                index({
                                    query:
                                        option.value === 'all'
                                            ? {}
                                            : { filter: option.value },
                                })
                            "
                            preserve-scroll
                        >
                            {{ trans(option.label) }}
                            <span
                                v-if="option.value === 'unread' && unreadCount"
                                class="tabular-nums opacity-80"
                            >
                                ({{ formatNumber(unreadCount) }})
                            </span>
                        </Link>
                    </Button>
                </div>

                <div
                    v-if="!notifications.data.length"
                    class="flex flex-col items-center gap-2 rounded-lg border border-dashed py-12 text-center text-muted-foreground"
                >
                    <BellOff class="size-8 opacity-60" />
                    <p class="text-sm">
                        {{
                            filter === 'unread'
                                ? trans('You have no unread notifications.')
                                : trans('You have no notifications.')
                        }}
                    </p>
                </div>

                <ul v-else class="divide-y rounded-lg border">
                    <li
                        v-for="notification in notifications.data"
                        :key="notification.id"
                        class="flex items-start gap-3 p-4 transition-colors"
                        :class="{ 'bg-muted/30': !notification.is_read }"
                    >
                        <NotificationListItem :notification="notification" />
                        <div class="flex shrink-0 items-center gap-1">
                            <Button
                                v-if="notification.action_url"
                                variant="ghost"
                                size="icon-sm"
                                :aria-label="trans('Open')"
                                :title="trans('Open')"
                                @click="openNotification(notification)"
                            >
                                <ExternalLink />
                            </Button>
                            <Button
                                v-if="!notification.is_read"
                                variant="ghost"
                                size="icon-sm"
                                :aria-label="trans('Mark as read')"
                                :title="trans('Mark as read')"
                                @click="markAsRead(notification)"
                            >
                                <Check />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon-sm"
                                class="text-muted-foreground hover:text-destructive"
                                :aria-label="trans('Delete')"
                                :title="trans('Delete')"
                                @click="deleteNotification(notification)"
                            >
                                <Trash2 />
                            </Button>
                        </div>
                    </li>
                </ul>

                <div
                    v-if="notifications.meta.last_page > 1"
                    class="flex flex-col items-center justify-between gap-3 text-sm text-muted-foreground sm:flex-row"
                >
                    <span class="tabular-nums">
                        {{
                            trans('Showing :from–:to of :total', {
                                from: formatNumber(
                                    notifications.meta.from ?? 0,
                                ),
                                to: formatNumber(notifications.meta.to ?? 0),
                                total: formatNumber(notifications.meta.total),
                            })
                        }}
                    </span>
                    <div class="flex items-center gap-2">
                        <Button
                            variant="outline"
                            size="icon-sm"
                            :aria-label="trans('Previous page')"
                            as-child
                        >
                            <Link
                                :href="notifications.links.prev ?? ''"
                                :class="{
                                    'pointer-events-none opacity-50':
                                        !notifications.links.prev,
                                }"
                                preserve-scroll
                            >
                                <ChevronLeft />
                            </Link>
                        </Button>
                        <span class="tabular-nums">
                            {{
                                trans('Page :current of :last', {
                                    current: notifications.meta.current_page,
                                    last: notifications.meta.last_page,
                                })
                            }}
                        </span>
                        <Button
                            variant="outline"
                            size="icon-sm"
                            :aria-label="trans('Next page')"
                            as-child
                        >
                            <Link
                                :href="notifications.links.next ?? ''"
                                :class="{
                                    'pointer-events-none opacity-50':
                                        !notifications.links.next,
                                }"
                                preserve-scroll
                            >
                                <ChevronRight />
                            </Link>
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    </PageContainer>
</template>
