<?php

declare(strict_types=1);

use Tests\TestCase;

pest()->extend(TestCase::class);

const TRANSLATED_LOCALES = ['en', 'es', 'pt_BR'];
const TRANSLATED_PHP_FILES = ['messages', 'errors', 'auth', 'passwords', 'pagination', 'validation'];

/**
 * @param  array<string, mixed>  $translations
 * @return array<int, string>
 */
function translationKeys(array $translations, string $prefix = ''): array
{
    $keys = [];

    foreach ($translations as $key => $value) {
        is_array($value)
            ? array_push($keys, ...translationKeys($value, "{$prefix}{$key}."))
            : $keys[] = "{$prefix}{$key}";
    }

    sort($keys);

    return $keys;
}

test('every locale has the same keys in each translation file', function (string $file) {
    $keysByLocale = array_map(
        fn (string $locale): array => array_values(array_filter(
            translationKeys(require dirname(__DIR__, 2)."/lang/{$locale}/{$file}.php"),
            fn (string $key): bool => ! str_starts_with($key, 'attributes.'),
        )),
        array_combine(TRANSLATED_LOCALES, TRANSLATED_LOCALES),
    );

    expect($keysByLocale['es'])->toBe($keysByLocale['en'], "Keys of {$file}.php differ for es")
        ->and(array_diff($keysByLocale['en'], $keysByLocale['pt_BR']))->toBeEmpty();
})->with(TRANSLATED_PHP_FILES);

test('every locale keeps its translation placeholders', function (string $file) {
    $placeholdersByKey = fn (string $locale): array => collect(translationKeys(require dirname(__DIR__, 2)."/lang/{$locale}/{$file}.php"))
        ->reject(fn (string $key): bool => str_starts_with($key, 'attributes.'))
        ->mapWithKeys(function (string $key) use ($file, $locale): array {
            preg_match_all('/:[a-zA-Z]+/', (string) __("{$file}.{$key}", [], $locale), $matches);

            return [$key => collect($matches[0])->unique()->sort()->values()->all()];
        })
        ->all();

    $referencePlaceholders = $placeholdersByKey('en');

    foreach (['es', 'pt_BR'] as $locale) {
        expect($placeholdersByKey($locale))->toBe($referencePlaceholders, "Placeholders of {$file}.php differ for {$locale}");
    }
})->with(['messages', 'errors']);
