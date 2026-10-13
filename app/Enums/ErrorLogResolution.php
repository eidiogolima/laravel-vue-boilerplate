<?php

declare(strict_types=1);

namespace App\Enums;

enum ErrorLogResolution: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case All = 'all';
}
