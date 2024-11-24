<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmployeeCredsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'min:6',
            ],
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'email.max' => 'Die E-Mail-Adresse darf maximal 255 Zeichen lang sein.',
            'password.min' => 'Das Kennwort muss mindestens 6 Zeichen haben.',
        ];
    }
}
