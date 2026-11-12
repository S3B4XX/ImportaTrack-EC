<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('suppliers', 'name')->ignore($this->route('supplier'))],
            'country' => ['required', Rule::in(config('catalogs.countries'))],
            'email' => ['nullable', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9 ]{7,30}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'country.in' => 'Selecciona un país de la lista.',
            'phone.regex' => 'El teléfono solo puede tener números, espacios y el prefijo +.',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre', 'country' => 'país', 'email' => 'correo', 'phone' => 'teléfono'];
    }
}
