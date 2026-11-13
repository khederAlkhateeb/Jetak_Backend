<?php

namespace App\Http\Requests\Api\SuperAdmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class ListUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Super Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search'   => ['nullable', 'string', 'max:255'],
            'role'     => ['nullable', 'string', 'exists:roles,name'],
            'status'   => ['nullable', 'string', 'in:active,banned,suspended'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by'  => ['nullable', 'string', 'in:id,name,created_at'],
            'sort_dir' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function messages()
    {
        return [
            'search.string' => 'The search parameter must be a string.',
            'search.max' => 'The search parameter may not be greater than 255 characters.',
            'role.string' => 'The role parameter must be a string.',
            'role.exists' => 'The specified role does not exist.',
            'status.string' => 'The status parameter must be a string.',
            'status.in' => 'The status parameter must be one of the following: active, banned, suspended.',
            'per_page.integer' => 'The per_page parameter must be an integer.',
            'per_page.min' => 'The per_page parameter must be at least 1.',
            'per_page.max' => 'The per_page parameter may not be greater than 100.',
            'sort_by.string' => 'The sort_by parameter must be a string.',
            'sort_by.in' => 'The sort_by parameter must be one of the following: id, name, created_at.',
            'sort_dir.string' => 'The sort_dir parameter must be a string.',
            'sort_dir.in' => 'The sort_dir parameter must be one of the following: asc, desc.',
        ];
    }
}
