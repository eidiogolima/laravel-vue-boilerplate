<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\MakeProjectRelativePathAction;
use Tests\TestCase;

pest()->extend(TestCase::class);

it('strips the project base path', function () {
    expect(app(MakeProjectRelativePathAction::class)->handle(base_path('app/Models/User.php')))
        ->toBe('app/Models/User.php');
});

it('keeps paths outside the project untouched', function () {
    expect(app(MakeProjectRelativePathAction::class)->handle('/usr/share/php/file.php'))
        ->toBe('/usr/share/php/file.php');
});
