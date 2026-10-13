<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ErrorLog
 */
class ErrorLogDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status_code' => $this->status_code,
            'exception' => $this->exception,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'code_snippet' => $this->code_snippet ?? [],
            'trace' => $this->trace ?? [],
            'context' => $this->context->value,
            'method' => $this->method,
            'url' => $this->url,
            'route' => $this->route,
            'ip' => $this->ip,
            'user_agent' => $this->user_agent,
            'user' => $this->user !== null ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar,
            ] : null,
            'payload' => (object) ($this->payload ?? []),
            'headers' => (object) ($this->headers ?? []),
            'environment' => $this->environment,
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
