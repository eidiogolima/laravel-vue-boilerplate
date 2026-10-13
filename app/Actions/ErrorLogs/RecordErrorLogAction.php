<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Enums\ErrorLogContext;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class RecordErrorLogAction
{
    private const int MAX_MESSAGE_LENGTH = 5000;

    public function __construct(
        private readonly MakeProjectRelativePathAction $makeProjectRelativePath,
        private readonly ExtractCodeSnippetAction $extractCodeSnippet,
        private readonly ExtractStackFramesAction $extractStackFrames,
        private readonly RedactSensitiveDataAction $redactSensitiveData,
    ) {}

    public function handle(Throwable $exception): void
    {
        try {
            $request = $this->currentHttpRequest();

            ErrorLog::query()->create([
                'hash' => sha1($exception::class.'|'.$exception->getFile().'|'.$exception->getLine()),
                'status_code' => $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500,
                'exception' => $exception::class,
                'message' => Str::limit($exception->getMessage(), self::MAX_MESSAGE_LENGTH),
                'file' => $this->makeProjectRelativePath->handle($exception->getFile()),
                'line' => $exception->getLine(),
                'code_snippet' => $this->extractCodeSnippet->handle($exception->getFile(), $exception->getLine()),
                'trace' => $this->extractStackFrames->handle($exception),
                'context' => $request instanceof Request ? ErrorLogContext::Http : ErrorLogContext::Console,
                'method' => $request?->method(),
                'url' => $request?->fullUrl(),
                'route' => $request?->route()?->getName() ?? $request?->route()?->uri(),
                'ip' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'user_id' => auth()->id(),
                'payload' => $request instanceof Request ? $this->redactSensitiveData->handle($request->except('_token')) : null,
                'headers' => $request instanceof Request ? $this->redactSensitiveData->handle($this->flattenHeaders($request)) : null,
                'environment' => app()->environment(),
            ]);
        } catch (Throwable) {
            return;
        }
    }

    private function currentHttpRequest(): ?Request
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return null;
        }

        return request();
    }

    /**
     * @return array<string, string>
     */
    private function flattenHeaders(Request $request): array
    {
        return collect($request->headers->all())
            ->map(fn (array $values): string => implode(', ', array_filter($values, is_string(...))))
            ->all();
    }
}
