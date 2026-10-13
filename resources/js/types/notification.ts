export type NotificationLevel = 'info' | 'success' | 'warning' | 'error';

export type NotificationFilter = 'all' | 'unread';

export type AppNotification = {
    id: string;
    title: string;
    message: string;
    level: NotificationLevel;
    action_url: string | null;
    is_read: boolean;
    read_at: string | null;
    created_at: string | null;
};
