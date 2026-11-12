@extends('layouts.app', ['title' => $supplier->exists ? 'Editar proveedor' : 'Nuevo proveedor'])

@section('content')
    <x-page-header :title="$supplier->exists ? 'Editar proveedor' : 'Nuevo proveedor'" />

    <x-panel as="form" method="POST" novalidate
          action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}"
          class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2">
        @csrf
        @if ($supplier->exists) @method('PUT') @endif

        <x-field name="name" label="Nombre" required class="md:col-span-2">
            <x-input name="name" value="{{ old('name', $supplier->name) }}" />
        </x-field>

        <x-field name="country" label="País" required>
            <x-input type="select" name="country">
                <option value="">Selecciona un país</option>
                @foreach ($countries as $country)
                    <option value="{{ $country }}" @selected(old('country', $supplier->country) === $country)>{{ $country }}</option>
                @endforeach
            </x-input>
        </x-field>

        <x-field name="phone" label="Teléfono" hint="Con código de país, ej. +86 755 8888 1234">
            <x-input name="phone" value="{{ old('phone', $supplier->phone) }}" inputmode="tel" />
        </x-field>

        <x-field name="email" label="Correo" class="md:col-span-2">
            <x-input name="email" type="email" value="{{ old('email', $supplier->email) }}" />
        </x-field>

        <x-form-actions :cancel="route('admin.suppliers.index')" :submit="$supplier->exists ? 'Guardar cambios' : 'Crear proveedor'" />
    </x-panel>
@endsection
