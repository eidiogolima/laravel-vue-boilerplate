<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use Illuminate\Support\Str;

class MakeProjectRelativePathAction
{
    public function handle(string $absolutePath): string
    {
        return Str::after($absolutePath, base_path().DIRECTORY_SEPARATOR);
    }
}
