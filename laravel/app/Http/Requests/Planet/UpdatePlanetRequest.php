<?php

declare(strict_types=1);

namespace App\Http\Requests\Planet;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in controller via policy
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
            'planet_name' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9 _-]+$/',
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
            'planet_name.regex' => 'Planet name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'planet_name.min' => 'Planet name must be at least 3 characters.',
            'planet_name.max' => 'Planet name cannot exceed 20 characters.',
        ];
    }
}
