<?php

declare(strict_types=1);

use App\Enums\SupportedLocale;

test('options list every supported locale with its native label', function () {
    expect(SupportedLocale::options())->toBe([
        ['value' => 'en', 'label' => 'English'],
        ['value' => 'es', 'label' => 'Español'],
        ['value' => 'pt_BR', 'label' => 'Português (Brasil)'],
    ]);
});
