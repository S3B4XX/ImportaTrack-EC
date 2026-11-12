@extends('layouts.app', ['title' => 'Usuarios'])

@section('content')
    <x-page-header title="Usuarios" subtitle="Administradores, operadores y clientes (importadores) de la agencia.">
        <x-slot:actions>
            <x-button :href="route('admin.users.create')">+ Nuevo usuario</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-panel as="form" method="GET" class="mb-4 grid grid-cols-1 gap-3 p-3 sm:grid-cols-[1fr_200px_auto]">
        {{-- sr-only: la etiqueta no se ve, pero los lectores de pantalla sí la leen --}}
        <label for="q" class="sr-only">Buscar usuario</label>
        <x-input name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, correo, empresa o RUC" />
        <label for="role" class="sr-only">Filtrar por rol</label>
        <x-input type="select" name="role">
            <option value="">Todos los roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(request('role') == $role->id)>{{ $role->name }}</option>
            @endforeach
        </x-input>
        <x-button variant="secundario">Filtrar</x-button>
    </x-panel>

    @if ($users->isEmpty())
        <x-empty>No se encontraron usuarios.</x-empty>
    @else
        {{-- Móvil: tarjetas --}}
        <div class="space-y-3 md:hidden">
            @foreach ($users as $user)
                <x-panel as="article" class="p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold">{{ $user->full_name }}</p>
                            <p class="truncate text-sm text-texto-suave">{{ $user->email }}</p>
                        </div>
                        <x-badge>{{ $user->role->name }}</x-badge>
                    </div>
                    @if ($user->company_name || $user->ruc)
                        <p class="mt-2 text-sm text-texto-suave">{{ $user->company_name }}</p>
                        <p class="font-mono text-xs text-texto-suave">{{ $user->ruc }}</p>
                    @endif
                    <div class="mt-3 flex gap-2">
                        <x-button variant="secundario" :href="route('admin.users.edit', $user)" class="flex-1">Editar</x-button>
                        <x-delete-button :action="route('admin.users.destroy', $user)" :name="$user->full_name" />
                    </div>
                </x-panel>
            @endforeach
        </div>

        {{-- Escritorio: tabla --}}
        <div class="hidden md:block">
            <x-table caption="Usuarios registrados">
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Empresa</th>
                    <th scope="col">RUC / Cédula</th>
                    <th scope="col" class="num">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <p class="font-medium">{{ $user->full_name }}</p>
                            <p class="text-xs text-texto-suave">{{ $user->email }}</p>
                        </td>
                        <td><x-badge>{{ $user->role->name }}</x-badge></td>
                        <td>{{ $user->company_name ?? '—' }}</td>
                        <td class="font-mono text-xs">{{ $user->ruc ?? '—' }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <x-button variant="secundario" :href="route('admin.users.edit', $user)">Editar</x-button>
                                <x-delete-button :action="route('admin.users.destroy', $user)" :name="$user->full_name" />
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    @endif
@endsection
