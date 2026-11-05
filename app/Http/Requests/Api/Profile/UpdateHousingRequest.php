<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHousingRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'phone' => ['nullable', 'string', 'max:20','unique:users,phone,' . $this->user()->id],
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
     *   "email.unique": string,
     *   "floor.integer": string,
     *   "floor.max": string,
     *   "phone.max": string,
     *   "phone.string": string,
     *   "phone.unique": string,
     *   "room_number.integer": string,
     *   "room_number.max": string
     * }
     */
    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.unique' => 'This phone number is already in use.',
            'building_number.integer' => 'Building number must be an integer.',
            'building_number.max' => 'Building number must not exceed 30.',
            'floor.integer' => 'Floor must be an integer.',
            'floor.max' => 'Floor must not exceed 10.',
            'room_number.integer' => 'Room number must be an integer.',
            'room_number.max' => 'Room number must not exceed 30.',
        ];
    }
}
