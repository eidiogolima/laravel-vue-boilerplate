<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ErrorLogContext;
use Database\Factories\ErrorLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $hash
 * @property int $status_code
 * @property string $exception
 * @property string|null $message
 * @property string|null $file
 * @property int|null $line
 * @property array<int, array{line: int, code: string}>|null $code_snippet
 * @property array<int, array{file: string|null, line: int|null, function: string, is_vendor: bool}>|null $trace
 * @property ErrorLogContext $context
 * @property string|null $method
 * @property string|null $url
 * @property string|null $route
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int|null $user_id
 * @property array<array-key, mixed>|null $payload
 * @property array<string, string>|null $headers
 * @property string $environment
 * @property Carbon|null $resolved_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 */
#[Fillable([
    'hash',
    'status_code',
    'exception',
    'message',
    'file',
    'line',
    'code_snippet',
    'trace',
    'context',
    'method',
    'url',
    'route',
    'ip',
    'user_agent',
    'user_id',
    'payload',
    'headers',
    'environment',
    'resolved_at',
])]
class ErrorLog extends Model
{
    /** @use HasFactory<ErrorLogFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'line' => 'integer',
            'code_snippet' => 'array',
            'trace' => 'array',
            'context' => ErrorLogContext::class,
            'payload' => 'array',
            'headers' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }
}
