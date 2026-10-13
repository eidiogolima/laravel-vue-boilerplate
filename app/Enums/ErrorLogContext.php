<?php

declare(strict_types=1);

namespace App\Enums;

enum ErrorLogContext: string
{
    case Http = 'http';
    case Console = 'console';
}
