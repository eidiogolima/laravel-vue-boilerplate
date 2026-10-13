<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\ErrorLogs;

use App\Models\ErrorLog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TriggerTestErrorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('triggerTestError', ErrorLog::class) ?? false;
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [];
    }
}
