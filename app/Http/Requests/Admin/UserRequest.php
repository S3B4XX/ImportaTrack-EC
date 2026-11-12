<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Rules\EcuadorianId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validación en back-end del formulario de usuarios.
 * El RUC/cédula es el dato sensible: se valida aquí (no en JavaScript) antes de guardarse.
 */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Solo se limpia lo que llegó como texto. Si alguien envía un arreglo (ruc[]=...),
        // se deja como está para que la regla 'string' lo rechace como error de validación.
        $email = $this->input('email');
        $ruc = $this->input('ruc');

        $this->merge([
            'email' => is_string($email) ? mb_strtolower(trim($email)) : $email,
            'ruc' => is_string($ruc) ? preg_replace('/\s+/', '', $ruc) : $ruc,
        ]);
    }

    public function isClient(): bool
    {
        return Role::whereKey($this->input('role_id'))->value('code') === Role::CLIENT;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $isClient = $this->isClient();

        return [
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            // bail: si una regla falla, no se evalúan las siguientes de ese campo.
            'email' => ['bail', 'required', 'string', 'email', 'max:200', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9 ]{7,20}$/'],
            'company_name' => [Rule::requiredIf($isClient), 'nullable', 'string', 'max:150'],
            'ruc' => [
                'bail',
                Rule::requiredIf($isClient),
                'nullable',
                'string',
                // Cliente (importador): exige RUC de 13 dígitos. Otros roles: cédula o RUC.
                new EcuadorianId($isClient ? EcuadorianId::RUC : EcuadorianId::ANY),
                Rule::unique('users', 'ruc')->ignore($user),
            ],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'La empresa es obligatoria para los clientes (importadores).',
            'ruc.required' => 'El RUC es obligatorio para los clientes (importadores).',
            'ruc.unique' => 'Ya existe un usuario registrado con este RUC/cédula.',
            'phone.regex' => 'El teléfono solo puede tener números, espacios y el prefijo +.',
        ];
    }

    public function attributes(): array
    {
        return [
            'role_id' => 'rol',
            'first_name' => 'nombre',
            'last_name' => 'apellido',
            'email' => 'correo',
            'phone' => 'teléfono',
            'company_name' => 'empresa',
            'ruc' => 'RUC/cédula',
            'password' => 'contraseña',
        ];
    }
}
