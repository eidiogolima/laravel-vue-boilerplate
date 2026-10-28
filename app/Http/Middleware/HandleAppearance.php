<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    public static function isPublicPage(string $component): bool
    {
        return in_array($component, ['Welcome', 'ErrorPage'], true) || str_starts_with($component, 'auth/');
    }

    public static function appearanceFor(string $component, string $storedAppearance): string
    {
        return self::isPublicPage($component) ? 'system' : $storedAppearance;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('appearance', $request->cookie('appearance') ?? 'system');

        return $next($request);
    }
}
