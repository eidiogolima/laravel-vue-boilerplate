<?php

declare(strict_types=1);

namespace App\Enums;

enum HttpStatusCodeRange: string
{
    case ServerError = '5xx';
    case ClientError = '4xx';

    /**
     * @return array{int, int}
     */
    public function bounds(): array
    {
        return match ($this) {
            self::ServerError => [500, 599],
            self::ClientError => [400, 499],
        };
    }
}
