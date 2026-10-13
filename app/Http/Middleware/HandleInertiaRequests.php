<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\ErrorLogs\CountOpenErrorLogsAction;
use App\Actions\Notifications\CountUnreadNotificationsAction;
use App\Actions\Notifications\ListUserNotificationsAction;
use App\Http\Resources\NotificationResource;
use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    private const int RECENT_NOTIFICATIONS_LIMIT = 5;

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'locale' => app()->getLocale(),
            'auth' => [
                'user' => $request->user(),
                'can' => [
                    'accessAdminPanel' => $request->user()?->can('viewAny', User::class) ?? false,
                ],
            ],
            'unreadNotificationsCount' => fn (): int => $request->user()
                ? app(CountUnreadNotificationsAction::class)->handle($request->user())
                : 0,
            'openErrorLogsCount' => fn (): int => $request->user()?->can('viewAny', ErrorLog::class)
                ? app(CountOpenErrorLogsAction::class)->handle()
                : 0,
            'recentNotifications' => Inertia::optional(fn (): ?AnonymousResourceCollection => $request->user()
                ? NotificationResource::collection(
                    app(ListUserNotificationsAction::class)->handle($request->user(), perPage: self::RECENT_NOTIFICATIONS_LIMIT),
                )
                : null),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
