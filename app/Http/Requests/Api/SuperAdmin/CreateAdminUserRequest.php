<?php

namespace App\Http\Requests\Api\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    /**
     * @return array{email: string[], name: string[], password: array<Password|string>, phone: string[], role: array<string|\Illuminate\Validation\Rules\In>, status: array<string|\Illuminate\Validation\Rules\In>}
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string','min:8'],
            'role'     => [
                'required',
                'string',
                Rule::in(['Store Admin', 'Hall Admin', 'Delivery']),
                'exists:roles,name',
            ],
            'status'   => ['nullable', 'string', Rule::in(['active', 'suspended', 'banned'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'phone.required' => 'The phone field is required.',
            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than 20 characters.',
            'phone.unique' => 'The phone has already been taken.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
            'role.required' => 'The role field is required.',
            'role.string' => 'The role must be a string.',
            'role.in' => "The selected role is invalid. Allowed values are: Store Admin, Hall Admin, Delivery.",
            'role.exists' => "The selected role does not exist.",
            'status.string' => "The status must be a string.",
            'status.in' => "The selected status is invalid. Allowed values are: active, suspended.",
        ];
    }
}
