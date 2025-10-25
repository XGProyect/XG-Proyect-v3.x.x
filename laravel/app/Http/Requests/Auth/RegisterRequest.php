<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => [
                'required',
                'string',
                'min:3',
                'max:32',
                'unique:users,user_name',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'user_email' => [
                'required',
                'string',
                'email',
                'max:64',
                'unique:users,user_email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'preference_lang' => [
                'sometimes',
                'string',
                'in:en,es,de,fr',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_name.regex' => 'Username can only contain letters, numbers, and underscores.',
            'user_name.unique' => 'This username is already taken.',
            'user_email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
