<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/** Validación del formulario de inicio de sesión. */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cualquier visitante puede intentar iniciar sesión.
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Los correos se guardan en minúsculas (ver UserRequest), así que se compara igual.
        $email = $this->input('email');

        $this->merge(['email' => is_string($email) ? mb_strtolower(trim($email)) : $email]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return ['email' => 'correo', 'password' => 'contraseña'];
    }
}
