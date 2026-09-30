<?php

declare(strict_types=1);

test('every translation key used in the frontend has a pt_BR translation', function () {
    $translations = json_decode(file_get_contents(dirname(__DIR__, 2).'/lang/pt_BR.json'), true);
    $sourceFiles = new RegexIterator(
        new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__, 2).'/resources/js')),
        '#/resources/js/(?!actions/|routes/|wayfinder/).+\.(vue|ts)$#',
    );

    $missingKeys = [];

    foreach ($sourceFiles as $sourceFile) {
        $source = file_get_contents($sourceFile->getPathname());

        preg_match_all('/trans\(\s*(\'|")((?:(?!\1).)+)\1/s', $source, $translatedCalls);
        preg_match_all('/\b(?:title|description|buttonText|label):\s*(\'|")((?:(?!\1).)+)\1/s', $source, $translatableProperties);

        foreach ([...$translatedCalls[2], ...$translatableProperties[2]] as $key) {
            if (! array_key_exists($key, $translations)) {
                $missingKeys[] = $key;
            }
        }
    }

    expect(array_unique($missingKeys))->toBeEmpty();
});
