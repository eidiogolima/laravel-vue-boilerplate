<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\Locale\RememberLocaleAction;
use App\Actions\Locale\ResolveLocaleAction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(private readonly ResolveLocaleAction $resolveLocale) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cookieLocale = $request->cookie(RememberLocaleAction::COOKIE_NAME);

        app()->setLocale($this->resolveLocale->handle(is_string($cookieLocale) ? $cookieLocale : null)->value);

        return $next($request);
    }
}
