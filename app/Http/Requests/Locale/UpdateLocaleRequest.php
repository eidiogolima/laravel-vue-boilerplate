<?php

declare(strict_types=1);

namespace App\Http\Requests\Locale;

use App\Enums\SupportedLocale;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', Rule::enum(SupportedLocale::class)],
        ];
    }

    public function locale(): SupportedLocale
    {
        return SupportedLocale::from($this->validated('locale'));
    }
}
