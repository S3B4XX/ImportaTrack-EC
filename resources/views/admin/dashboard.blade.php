@extends('layouts.app', ['title' => 'Administración'])

@use('App\Support\Money')

@section('content')
    <x-page-header title="Administración" subtitle="Los catálogos que mantienes aquí alimentan la liquidación de cada importación (Core)." />

    {{-- Indicadores: una sola lista con líneas de 1 px entre celdas (gap-px sobre fondo "borde") --}}
    <ul class="grid grid-cols-2 gap-px overflow-hidden rounded-md border border-borde bg-borde md:grid-cols-4" aria-label="Resumen">
        @foreach ($stats as $stat)
            <li>
                <a href="{{ route($stat['route']) }}" class="block h-full bg-superficie p-3 hover:bg-fondo focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primario">
                    <span class="block text-xs text-texto-suave">{{ $stat['label'] }}</span>
                    <span class="mt-1 block font-mono text-2xl font-semibold tabular-nums">{{ $stat['value'] }}</span>
                    <span class="mt-1 block text-xs text-texto-suave">{{ $stat['hint'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <x-panel class="self-start md:col-span-1">
            <header class="flex items-center justify-between border-b border-borde px-4">
                <h2 class="text-sm font-semibold text-primario">Tasas vigentes</h2>
                <a href="{{ route('admin.tax-rates.index') }}" class="inline-flex min-h-11 items-center text-sm font-medium text-primario underline">Configurar</a>
            </header>
            <dl class="px-4 text-sm">
                @foreach ($taxRates as $rate)
                    <div class="flex items-center justify-between gap-3 border-b border-borde py-2 last:border-0">
                        <dt>
                            <span class="font-medium">{{ $rate->code }}</span>
                            <span class="block text-xs text-texto-suave">desde {{ $rate->valid_from->format('d/m/Y') }}</span>
                        </dt>
                        <dd class="font-mono text-lg font-semibold tabular-nums">{{ Money::formatPercent($rate->rate) }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-panel>

        <x-panel class="self-start md:col-span-2">
            <header class="flex items-center justify-between border-b border-borde px-4">
                <h2 class="text-sm font-semibold text-primario">Importaciones recientes</h2>
                <a href="{{ route('admin.imports.index') }}" class="inline-flex min-h-11 items-center text-sm font-medium text-primario underline">Ver todas</a>
            </header>
            <ul class="text-sm">
                @forelse ($recentImports as $import)
                    <li class="border-b border-borde last:border-0">
                        <a href="{{ route('admin.imports.show', $import) }}" class="flex min-h-11 items-center justify-between gap-3 px-4 py-2 hover:bg-fondo focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primario">
                            <span class="min-w-0">
                                <span class="block font-mono font-medium">{{ $import->code }}</span>
                                <span class="block truncate text-xs text-texto-suave">{{ $import->client->company_name }} · {{ $import->supplier->name }}</span>
                            </span>
                            <x-status-badge :status="$import->status" />
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-texto-suave">Aún no hay importaciones registradas.</li>
                @endforelse
            </ul>
        </x-panel>
    </div>
@endsection
