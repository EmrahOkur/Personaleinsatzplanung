<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÄäÖöÜüß\s\-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zÄäÖöÜüß\s\-]+$/'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email'),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[+]?[0-9\s\-()]+$/',
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'employee_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('employees', 'employee_number'),
            ],
            'hire_date' => ['required', 'date'],
            'exit_date' => ['nullable', 'date', 'after:hire_date'],
            'position' => ['required', 'string', 'max:255'],
            'vacation_days' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'in:active,inactive,on_leave'],
            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-zÄäÖöÜüß\s\-]+$/',
            ],
            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[+]?[0-9\s\-()]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Der Vorname ist erforderlich.',
            'first_name.max' => 'Der Vorname darf maximal 255 Zeichen lang sein.',
            'first_name.regex' => 'Der Vorname darf nur Buchstaben, Leerzeichen und Bindestriche enthalten.',

            'last_name.required' => 'Der Nachname ist erforderlich.',
            'last_name.max' => 'Der Nachname darf maximal 255 Zeichen lang sein.',
            'last_name.regex' => 'Der Nachname darf nur Buchstaben, Leerzeichen und Bindestriche enthalten.',

            'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'email.max' => 'Die E-Mail-Adresse darf maximal 255 Zeichen lang sein.',

            'phone.required' => 'Die Telefonnummer ist erforderlich.',
            'phone.regex' => 'Die Telefonnummer hat ein ungültiges Format.',

            'birth_date.required' => 'Das Geburtsdatum ist erforderlich.',
            'birth_date.before' => 'Das Geburtsdatum muss in der Vergangenheit liegen.',

            'employee_number.required' => 'Die Personalnummer ist erforderlich.',
            'employee_number.unique' => 'Diese Personalnummer wird bereits verwendet.',
            'employee_number.regex' => 'Die Personalnummer darf nur Großbuchstaben, Zahlen und Bindestriche enthalten.',

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
            'emergency_contact_name.regex' => 'Der Name des Notfallkontakts darf nur Buchstaben, Leerzeichen und Bindestriche enthalten.',

            'emergency_contact_phone.required' => 'Die Telefonnummer des Notfallkontakts ist erforderlich.',
            'emergency_contact_phone.regex' => 'Die Telefonnummer des Notfallkontakts hat ein ungültiges Format.',
        ];
    }
}
