<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20','unique:users,phone'],
            'building_number' => ['nullable', 'integer', 'max:30'],
            'floor' => ['nullable', 'integer', 'max:10'],
            'room_number' => ['nullable', 'integer', 'max:30'],
        ];
    }


    /**
     * @return array{
     *   "building_number.integer": string,
     *   "building_number.max": string,
     *   "email.email": string,
     *   "email.required": string,
     *   "email.unique": string,
     *   "floor.integer": string,
     *   "floor.max": string,
     *   "name.required": string,
     *   "password.min": string,
     *   "password.required": string,
     *   "phone.max": string,
     *   "phone.string": string,
     *   "room_number.integer": string,
     *   "room_number.max": string
     * }
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email has already been taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'phone.string' => 'Phone must be a string.',
            'phone.max' => 'Phone may not be greater than 20 characters.',
            'building_number.integer' => 'Building number must be an integer.',
            'building_number.max' => 'Building number may not be greater than 30.',
            'floor.integer' => 'Floor must be an integer.',
            'floor.max' => 'Floor may not be greater than 10.',
            'room_number.integer' => 'Room number must be an integer.',
            'room_number.max' => 'Room number may not be greater than 10.',
        ];
    }
}
