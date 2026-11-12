@extends('layouts.app', ['title' => 'Tasas de tributos'])

@use('App\Support\Money')

@section('content')
    <x-page-header title="Tasas de tributos"
                   subtitle="La liquidación usa estas tasas: IVA × (CIF + Ad Valorem + FODINFA) y FODINFA × CIF." />

    {{-- Móvil: cada tasa como lista término → valor, para no deslizar la tabla hacia los lados --}}
    <div class="space-y-3 md:hidden">
        @foreach ($taxRates as $rate)
            <x-panel as="article" class="p-3">
                <h2 class="font-semibold">{{ $rate->code }} <span class="text-sm font-normal text-texto-suave">· {{ $rate->name }}</span></h2>
                <dl class="mt-2 text-sm">
                    <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Tasa</dt><dd class="font-mono font-semibold tabular-nums">{{ Money::formatPercent($rate->rate) }}</dd></div>
                    <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Vigente desde</dt><dd class="font-mono tabular-nums">{{ $rate->valid_from->format('d/m/Y') }}</dd></div>
                </dl>
                <x-button variant="secundario" :href="route('admin.tax-rates.edit', $rate)" class="mt-2 w-full">Editar tasa</x-button>
            </x-panel>
        @endforeach
    </div>

    {{-- Escritorio: son datos tabulares, así que van en una tabla --}}
    <div class="hidden md:block">
        <x-table caption="Tasas de tributos vigentes">
            <thead>
            <tr>
                <th scope="col">Tributo</th>
                <th scope="col" class="num">Tasa</th>
                <th scope="col" class="num">Vigente desde</th>
                <th scope="col" class="num">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($taxRates as $rate)
                <tr>
                    <td>
                        <p class="font-semibold">{{ $rate->code }}</p>
                        <p class="text-xs text-texto-suave">{{ $rate->name }}</p>
                    </td>
                    <td class="num font-semibold">{{ Money::formatPercent($rate->rate) }}</td>
                    <td class="num">{{ $rate->valid_from->format('d/m/Y') }}</td>
                    <td>
                        <div class="flex justify-end">
                            <x-button variant="secundario" :href="route('admin.tax-rates.edit', $rate)">Editar</x-button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </x-table>
    </div>
@endsection
