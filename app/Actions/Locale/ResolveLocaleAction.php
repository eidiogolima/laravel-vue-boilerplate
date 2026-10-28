<?php

declare(strict_types=1);

namespace App\Actions\Locale;

use App\Enums\SupportedLocale;

class ResolveLocaleAction
{
    public function handle(?string $preferredLocale): SupportedLocale
    {
        return SupportedLocale::tryFrom((string) $preferredLocale)
            ?? SupportedLocale::tryFrom(config('app.locale'))
            ?? SupportedLocale::English;
    }
}
