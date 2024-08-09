<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['sometimes', 'nullable', 'string'],
            'newPassword' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
