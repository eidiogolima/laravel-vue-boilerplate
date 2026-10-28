<?php

declare(strict_types=1);

namespace App\Enums;

enum SupportedLocale: string
{
    case English = 'en';
    case Spanish = 'es';
    case BrazilianPortuguese = 'pt_BR';

    public function label(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Spanish => 'Español',
            self::BrazilianPortuguese => 'Português (Brasil)',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $locale): array => ['value' => $locale->value, 'label' => $locale->label()],
            self::cases(),
        );
    }
}
