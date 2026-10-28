<?php

declare(strict_types=1);

use App\Actions\Locale\ResolveLocaleAction;
use App\Enums\SupportedLocale;
use Tests\TestCase;

pest()->extend(TestCase::class);

it('resolves a supported preferred locale', function (string $preferredLocale, SupportedLocale $expectedLocale) {
    expect(app(ResolveLocaleAction::class)->handle($preferredLocale))->toBe($expectedLocale);
})->with([
    ['en', SupportedLocale::English],
    ['es', SupportedLocale::Spanish],
    ['pt_BR', SupportedLocale::BrazilianPortuguese],
]);

it('falls back to the configured locale when the preferred locale is missing', function (?string $preferredLocale) {
    config()->set('app.locale', 'es');

    expect(app(ResolveLocaleAction::class)->handle($preferredLocale))->toBe(SupportedLocale::Spanish);
})->with([null, '', 'fr']);

it('falls back to english when the configured locale is not supported', function () {
    config()->set('app.locale', 'fr');

    expect(app(ResolveLocaleAction::class)->handle(null))->toBe(SupportedLocale::English);
});
