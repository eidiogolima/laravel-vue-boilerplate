import { router } from '@inertiajs/vue3';
import { destroy, read, readAll } from '@/routes/notifications';
import type { AppNotification } from '@/types';

type NotificationActionsOptions = {
    reloadOnly?: string[];
};

function isSameOriginUrl(url: string): boolean {
    return (
        new URL(url, window.location.origin).origin === window.location.origin
    );
}

function followActionUrl(actionUrl: string): void {
    if (isSameOriginUrl(actionUrl)) {
        router.visit(actionUrl);

        return;
    }

    window.location.assign(actionUrl);
}

export function useNotificationActions(
    options: NotificationActionsOptions = {},
) {
    const visitOptions = {
        preserveScroll: true,
        preserveState: true,
        ...(options.reloadOnly ? { only: options.reloadOnly } : {}),
    };

    const markAsRead = (
        notification: AppNotification,
        onFinish?: () => void,
    ): void => {
        if (notification.is_read) {
            onFinish?.();

            return;
        }

        router.patch(
            read(notification.id).url,
            {},
            { ...visitOptions, onFinish },
        );
    };

    const openNotification = (notification: AppNotification): void => {
        const actionUrl = notification.action_url;

        markAsRead(
            notification,
            actionUrl ? () => followActionUrl(actionUrl) : undefined,
        );
    };

    const markAllAsRead = (): void => {
        router.post(readAll().url, {}, visitOptions);
    };

    const deleteNotification = (notification: AppNotification): void => {
        router.delete(destroy(notification.id).url, visitOptions);
    };

    return { markAsRead, openNotification, markAllAsRead, deleteNotification };
}
