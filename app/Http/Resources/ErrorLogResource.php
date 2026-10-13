<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @mixin ErrorLog
 */
class ErrorLogResource extends JsonResource
{
    private const int MESSAGE_PREVIEW_LENGTH = 240;

    /**
     * @return array<string, int|string|bool|null>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status_code' => $this->status_code,
            'exception' => class_basename($this->exception),
            'message' => Str::limit((string) $this->message, self::MESSAGE_PREVIEW_LENGTH),
            'file' => $this->file,
            'line' => $this->line,
            'method' => $this->method,
            'url' => $this->url !== null ? Str::after($this->url, $request->getSchemeAndHttpHost()) : null,
            'context' => $this->context->value,
            'user_name' => $this->user?->name,
            'is_resolved' => $this->isResolved(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
