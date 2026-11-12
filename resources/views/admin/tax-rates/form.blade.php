@extends('layouts.app', ['title' => 'Editar '.$taxRate->code])

@section('content')
    <x-page-header :title="'Editar tasa '.$taxRate->code"
                   subtitle="El cambio se aplica de inmediato a todas las liquidaciones." />

    <x-panel as="form" method="POST" novalidate action="{{ route('admin.tax-rates.update', $taxRate) }}" class="grid max-w-2xl grid-cols-1 gap-4 p-4 md:grid-cols-2">
        @csrf
        @method('PUT')

        <x-field name="code" label="Código">
            <x-input name="code" value="{{ $taxRate->code }}" disabled />
        </x-field>

        <x-field name="rate" label="Tasa (%)" required>
            <x-input name="rate" value="{{ old('rate', $taxRate->rate) }}" inputmode="decimal" />
        </x-field>

        <x-field name="name" label="Nombre" required class="md:col-span-2">
            <x-input name="name" value="{{ old('name', $taxRate->name) }}" />
        </x-field>

        <x-field name="valid_from" label="Vigente desde" required>
            <x-input name="valid_from" type="date" value="{{ old('valid_from', $taxRate->valid_from?->format('Y-m-d')) }}" />
        </x-field>

        <x-form-actions :cancel="route('admin.tax-rates.index')" submit="Guardar tasa" />
    </x-panel>
@endsection
