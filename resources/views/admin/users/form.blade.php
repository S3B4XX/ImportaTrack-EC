@extends('layouts.app', ['title' => $user->exists ? 'Editar usuario' : 'Nuevo usuario'])

@section('content')
    <x-page-header :title="$user->exists ? 'Editar usuario' : 'Nuevo usuario'"
                   subtitle="El RUC/cédula se valida en el servidor (dígito verificador del SRI) antes de guardarse." />

    {{-- novalidate: toda la validación la hace el back-end (UserRequest + regla EcuadorianId) --}}
    <form method="POST" novalidate
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}"
          class="space-y-5 rounded-md border border-borde bg-superficie p-4">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <fieldset class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <legend class="mb-2 text-sm font-semibold text-primario">Datos personales</legend>

            <x-field name="role_id" label="Rol" required class="md:col-span-2">
                {{-- Clave foránea: dropdown, nunca un input con el id --}}
                <x-input type="select" name="role_id" data-role-select>
                    <option value="">Selecciona un rol</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" data-code="{{ $role->code }}"
                            @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }} — {{ $role->description }}</option>
                    @endforeach
                </x-input>
            </x-field>

            <x-field name="first_name" label="Nombre" required>
                <x-input name="first_name" value="{{ old('first_name', $user->first_name) }}" />
            </x-field>

            <x-field name="last_name" label="Apellido" required>
                <x-input name="last_name" value="{{ old('last_name', $user->last_name) }}" />
            </x-field>

            <x-field name="email" label="Correo" required>
                <x-input name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="off" />
            </x-field>

            <x-field name="phone" label="Teléfono" hint="Ej. 0991234567 o +593 2 234 5678">
                <x-input name="phone" value="{{ old('phone', $user->phone) }}" inputmode="tel" />
            </x-field>
        </fieldset>

        <hr class="border-borde">
        <fieldset class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <legend class="mb-2 text-sm font-semibold text-primario">Datos tributarios</legend>

            <x-field name="ruc" label="RUC / Cédula" hint-id="ruc-hint"
                     hint="Clientes: RUC de 13 dígitos. Otros roles: cédula (10) o RUC (13), opcional.">
                <x-input name="ruc" value="{{ old('ruc', $user->ruc) }}" inputmode="numeric" autocomplete="off" class="font-mono" />
            </x-field>

            <x-field name="company_name" label="Empresa" hint="Obligatoria para clientes (importadores).">
                <x-input name="company_name" value="{{ old('company_name', $user->company_name) }}" />
            </x-field>
        </fieldset>

        <hr class="border-borde">
        <fieldset class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <legend class="mb-2 text-sm font-semibold text-primario">Acceso</legend>

            <x-field name="password" :label="$user->exists ? 'Nueva contraseña' : 'Contraseña'" :required="! $user->exists"
                     :hint="$user->exists ? 'Déjala vacía para mantener la actual.' : 'Mínimo 8 caracteres, con letras y números.'">
                <x-input name="password" type="password" autocomplete="new-password" />
            </x-field>

            <x-field name="password_confirmation" label="Confirmar contraseña">
                <x-input name="password_confirmation" type="password" autocomplete="new-password" />
            </x-field>
        </fieldset>

        <x-form-actions :cancel="route('admin.users.index')" :submit="$user->exists ? 'Guardar cambios' : 'Crear usuario'" />
    </form>
@endsection
