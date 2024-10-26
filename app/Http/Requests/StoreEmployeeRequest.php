<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true if no special authorization is needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'email' => [
                'email',
                Rule::unique('employees', 'email')->ignore($this->employee),
            ],
            'phone' => ['string', 'max:20'],
            'birth_date' => ['date', 'before:today'],
            'gender' => ['string', 'in:male,female,other'],
            'employee_number' => [
                'string',
                Rule::unique('employees', 'employee_number')->ignore($this->employee),
            ],
            'hire_date' => ['date'],
            'exit_date' => ['nullable', 'date', 'after:hire_date'],
            'position' => ['string', 'max:255'],
            'vacation_days' => ['integer', 'min:0', 'max:100'],
            'status' => ['string', 'in:active,inactive,on_leave'],
            'emergency_contact_name' => ['string', 'max:255'],
            'emergency_contact_phone' => ['string', 'max:20'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Der Vorname ist erforderlich.',
            'first_name.max' => 'Der Vorname darf maximal 255 Zeichen lang sein.',
            'last_name.required' => 'Der Nachname ist erforderlich.',
            'last_name.max' => 'Der Nachname darf maximal 255 Zeichen lang sein.',
            'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'phone.required' => 'Die Telefonnummer ist erforderlich.',
            'birth_date.required' => 'Das Geburtsdatum ist erforderlich.',
            'birth_date.before' => 'Das Geburtsdatum muss in der Vergangenheit liegen.',
            'gender.required' => 'Bitte wählen Sie ein Geschlecht aus.',
            'gender.in' => 'Ungültiger Wert für Geschlecht.',
            'employee_number.required' => 'Die Personalnummer ist erforderlich.',
            'employee_number.unique' => 'Diese Personalnummer wird bereits verwendet.',
            'hire_date.required' => 'Das Eintrittsdatum ist erforderlich.',
            'exit_date.after' => 'Das Austrittsdatum muss nach dem Eintrittsdatum liegen.',
            'position.required' => 'Die Position ist erforderlich.',
            'vacation_days.required' => 'Die Anzahl der Urlaubstage ist erforderlich.',
            'vacation_days.integer' => 'Die Urlaubstage müssen eine ganze Zahl sein.',
            'vacation_days.min' => 'Die Urlaubstage müssen mindestens 0 sein.',
            'vacation_days.max' => 'Die Urlaubstage dürfen maximal 100 sein.',
            'status.required' => 'Der Status ist erforderlich.',
            'status.in' => 'Ungültiger Wert für Status.',
            'emergency_contact_name.required' => 'Der Name des Notfallkontakts ist erforderlich.',
            'emergency_contact_phone.required' => 'Die Telefonnummer des Notfallkontakts ist erforderlich.',
        ];
    }
}
