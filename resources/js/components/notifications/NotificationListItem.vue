<script setup lang="ts">
import NotificationLevelIcon from '@/components/notifications/NotificationLevelIcon.vue';
import { formatRelativeTime } from '@/lib/formatters';
import { cn } from '@/lib/utils';
import type { AppNotification } from '@/types';

defineProps<{
    notification: AppNotification;
    compact?: boolean;
}>();
</script>

<template>
    <div class="flex w-full items-start gap-3 text-left">
        <NotificationLevelIcon :level="notification.level" />
        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            <div class="flex items-center gap-2">
                <span
                    :class="
                        cn(
                            'truncate text-sm',
                            notification.is_read
                                ? 'font-normal text-muted-foreground'
                                : 'font-medium text-foreground',
                        )
                    "
                >
                    {{ notification.title }}
                </span>
                <span
                    v-if="!notification.is_read"
                    class="size-2 shrink-0 rounded-full bg-primary"
                    aria-hidden="true"
                />
            </div>
            <p
                :class="
                    cn(
                        'text-sm text-muted-foreground',
                        compact && 'line-clamp-2 text-xs',
                    )
                "
            >
                {{ notification.message }}
            </p>
            <time
                v-if="notification.created_at"
                :datetime="notification.created_at"
                class="text-xs text-muted-foreground/80"
            >
                {{ formatRelativeTime(notification.created_at) }}
            </time>
        </div>
    </div>
</template>
