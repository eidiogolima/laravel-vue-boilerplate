<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\ErrorLogs;

use App\Models\ErrorLog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DestroyErrorLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delete', $this->errorLog()) ?? false;
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [];
    }

    public function errorLog(): ErrorLog
    {
        /** @var ErrorLog */
        return $this->route('errorLog');
    }
}
