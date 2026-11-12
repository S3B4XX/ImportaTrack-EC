<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validación de las tasas (IVA y FODINFA) que usa la liquidación del Core. */
class TaxRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'rate' => ['required', 'numeric', 'decimal:0,2', 'between:0,100'],
            'valid_from' => ['required', 'date', 'before_or_equal:'.now()->addYear()->toDateString()],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre', 'rate' => 'tasa', 'valid_from' => 'vigente desde'];
    }
}
