<?php

declare(strict_types=1);

use App\Actions\Locale\RememberLocaleAction;
use App\Enums\SupportedLocale;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

pest()->extend(TestCase::class);

it('queues a cookie with the chosen locale', function () {
    app(RememberLocaleAction::class)->handle(SupportedLocale::Spanish);

    expect(Cookie::hasQueued(RememberLocaleAction::COOKIE_NAME))->toBeTrue()
        ->and(Cookie::queued(RememberLocaleAction::COOKIE_NAME)->getValue())->toBe('es');
});
