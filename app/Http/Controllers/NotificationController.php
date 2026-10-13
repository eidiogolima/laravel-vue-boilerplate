<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Notifications\DeleteNotificationAction;
use App\Actions\Notifications\ListUserNotificationsAction;
use App\Actions\Notifications\MarkAllNotificationsAsReadAction;
use App\Actions\Notifications\MarkNotificationAsReadAction;
use App\Http\Requests\Notifications\ListNotificationsRequest;
use App\Http\Requests\Notifications\ManageNotificationRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(ListNotificationsRequest $request, ListUserNotificationsAction $listUserNotifications): Response
    {
        return Inertia::render('notifications/Index', [
            'notifications' => NotificationResource::collection(
                $listUserNotifications->handle($request->user(), $request->onlyUnread()),
            ),
            'filter' => $request->onlyUnread() ? 'unread' : 'all',
        ]);
    }

    public function markAsRead(ManageNotificationRequest $request, MarkNotificationAsReadAction $markNotificationAsRead): RedirectResponse
    {
        $markNotificationAsRead->handle($request->notification());

        return back();
    }

    public function markAllAsRead(Request $request, MarkAllNotificationsAsReadAction $markAllNotificationsAsRead): RedirectResponse
    {
        $markAllNotificationsAsRead->handle($request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('messages.notifications.all_marked_as_read')]);

        return back();
    }

    public function destroy(ManageNotificationRequest $request, DeleteNotificationAction $deleteNotification): RedirectResponse
    {
        $deleteNotification->handle($request->notification());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('messages.notifications.deleted')]);

        return back();
    }
}
