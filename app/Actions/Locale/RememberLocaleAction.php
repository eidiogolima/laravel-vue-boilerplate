<?php

declare(strict_types=1);

namespace App\Actions\Locale;

use App\Enums\SupportedLocale;
use Illuminate\Support\Facades\Cookie;

class RememberLocaleAction
{
    public const string COOKIE_NAME = 'locale';

    private const int LIFETIME_IN_MINUTES = 525600;

    public function handle(SupportedLocale $locale): void
    {
        Cookie::queue(self::COOKIE_NAME, $locale->value, self::LIFETIME_IN_MINUTES);
    }
}
