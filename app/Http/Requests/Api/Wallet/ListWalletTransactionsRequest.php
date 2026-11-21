<?php

namespace App\Http\Requests\Api\Wallet;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ListWalletTransactionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type'      => ['nullable', 'string', Rule::in(['deposit', 'spend'])],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
