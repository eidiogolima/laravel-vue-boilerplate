<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\ErrorLogs;

use App\Enums\ErrorLogResolution;
use App\Enums\HttpStatusCodeRange;
use App\Enums\SortOrder;
use App\Models\ErrorLog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class IndexErrorLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', ErrorLog::class) ?? false;
    }

    /**
     * @return array<string, array<int, ValidationRule|Enum|string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'resolution' => ['nullable', Rule::enum(ErrorLogResolution::class)],
            'status' => ['nullable', Rule::enum(HttpStatusCodeRange::class)],
            'sort' => ['nullable', Rule::enum(SortOrder::class)],
        ];
    }

    public function search(): ?string
    {
        return $this->string('search')->trim()->toString() ?: null;
    }

    public function resolution(): ErrorLogResolution
    {
        return $this->enum('resolution', ErrorLogResolution::class) ?? ErrorLogResolution::Open;
    }

    public function statusCodeRange(): ?HttpStatusCodeRange
    {
        return $this->enum('status', HttpStatusCodeRange::class);
    }

    public function sortOrder(): SortOrder
    {
        return $this->enum('sort', SortOrder::class) ?? SortOrder::Newest;
    }
}
