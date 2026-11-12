@extends('layouts.app', ['title' => $import->code])

@use('App\Support\Money')

@section('content')
    <x-page-header :title="$import->code" :subtitle="$import->client->company_name.' · '.$import->supplier->name.' ('.$import->supplier->country.')'">
        <x-slot:actions>
            <x-button variant="secundario" :href="route('admin.imports.index')">Volver al listado</x-button>
        </x-slot:actions>
    </x-page-header>

    {{--
        Resumen. Es una lista de definiciones (<dl>: dato → valor), no cuatro tarjetas sueltas.
        gap-px sobre fondo "borde" deja ver líneas de 1 px entre las celdas.
    --}}
    <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-md border border-borde bg-borde md:grid-cols-4">
        <div class="bg-superficie p-3">
            <dt class="text-xs text-texto-suave">Estado</dt>
            <dd class="mt-1"><x-status-badge :status="$import->status" /></dd>
            @if ($import->customs_channel)
                <dd class="mt-1 text-xs text-texto-suave">Aforo {{ mb_strtolower($import->customs_channel->label()) }}</dd>
            @endif
        </div>
        <div class="bg-superficie p-3">
            <dt class="text-xs text-texto-suave">Valor FOB</dt>
            <dd class="mt-1 text-lg font-semibold"><x-cifra :value="$totals['fob']" /></dd>
        </div>
        <div class="bg-superficie p-3">
            <dt class="text-xs text-texto-suave">Costo total puesto en bodega</dt>
            <dd class="mt-1 text-lg font-semibold"><x-cifra :value="$totals['total']" /></dd>
            <dd class="text-xs text-texto-suave">{{ number_format($totals['over_fob'] * 100, 1, ',', '') }}% sobre el FOB</dd>
        </div>
        <div class="bg-superficie p-3">
            <dt class="text-xs text-texto-suave">Cumplimiento</dt>
            <dd class="mt-1"><x-compliance-badge :compliance="$compliance" /></dd>
            <dd class="mt-1 text-xs text-texto-suave">
                @if ($compliance)
                    Desviación {{ number_format($deviation * 100, 1, ',', '') }}%
                @else
                    Se calcula al liberar la carga
                @endif
            </dd>
        </div>
    </dl>

    {{-- LIQUIDACIÓN: se presenta como un documento contable --}}
    <section class="mt-6">
        <h2 class="text-base font-semibold text-primario">Liquidación de importación</h2>
        <p class="mt-1 text-xs text-texto-suave">
            Tasas vigentes: IVA {{ Money::formatPercent($liquidation['rates']['iva']) }} ·
            FODINFA {{ Money::formatPercent($liquidation['rates']['fodinfa']) }} · Ad Valorem según cada partida.
        </p>

        {{-- Valores de toda la importación que se reparten entre los productos --}}
        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm md:grid-cols-4">
            <div><dt class="text-xs text-texto-suave">Flete</dt><dd><x-cifra :value="$import->freight" /></dd></div>
            <div><dt class="text-xs text-texto-suave">Seguro</dt><dd><x-cifra :value="$import->insurance" /></dd></div>
            <div><dt class="text-xs text-texto-suave"><abbr title="Costo, seguro y flete">CIF</abbr> total</dt><dd><x-cifra :value="$totals['cif']" /></dd></div>
            <div><dt class="text-xs text-texto-suave">Gastos locales</dt><dd><x-cifra :value="$import->local_costs" /></dd></div>
        </dl>

        {{--
            MÓVIL (Mobile First): una tabla de 10 columnas no cabe en 360 px y obligaría a
            deslizar hacia los lados. Por eso cada producto se muestra como una lista
            término → valor (<dl>), que se lee de arriba hacia abajo.
        --}}
        <div class="mt-4 space-y-3 md:hidden">
            @foreach ($liquidation['items'] as $row)
                <article class="rounded-md border border-borde bg-superficie">
                    <header class="border-b border-borde bg-fondo px-3 py-2">
                        <h3 class="text-sm font-semibold">{{ $row['description'] }} <span class="font-normal text-texto-suave">× {{ $row['quantity'] }}</span></h3>
                        <p class="font-mono text-xs text-texto-suave">{{ $row['tariff_code'] }} · Ad Valorem {{ Money::formatPercent($row['ad_valorem_rate']) }}</p>
                    </header>
                    <dl class="px-3 py-1 text-sm">
                        <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">FOB</dt><dd><x-cifra :value="$row['fob']" /></dd></div>
                        <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">CIF</dt><dd><x-cifra :value="$row['cif']" /></dd></div>
                        <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Ad Valorem</dt><dd><x-cifra :value="$row['ad_valorem']" /></dd></div>
                        <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">FODINFA</dt><dd><x-cifra :value="$row['fodinfa']" /></dd></div>
                        <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">IVA</dt><dd><x-cifra :value="$row['iva']" /></dd></div>
                        <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Gastos locales</dt><dd><x-cifra :value="$row['local_costs']" /></dd></div>
                        {{-- El borde superior separa los subtotales del detalle, como en un documento contable --}}
                        <div class="flex justify-between gap-3 border-t border-borde py-1 font-semibold"><dt>Costo total</dt><dd><x-cifra :value="$row['total']" /></dd></div>
                        <div class="flex justify-between gap-3 py-1 font-semibold"><dt>Costo unitario en bodega</dt><dd><x-cifra :value="$row['unit_cost']" /></dd></div>
                    </dl>
                </article>
            @endforeach

            <article class="rounded-md border border-borde border-t-2 border-t-primario bg-superficie">
                <h3 class="px-3 pt-2 text-sm font-semibold">Totales de la importación</h3>
                <dl class="px-3 py-1 text-sm">
                    <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">FOB</dt><dd><x-cifra :value="$totals['fob']" /></dd></div>
                    <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">CIF</dt><dd><x-cifra :value="$totals['cif']" /></dd></div>
                    <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Tributos pagados</dt><dd><x-cifra :value="$totals['taxes']" /></dd></div>
                    <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Gastos locales</dt><dd><x-cifra :value="$totals['local_costs']" /></dd></div>
                    <div class="flex justify-between gap-3 border-t border-borde py-1 font-semibold"><dt>Costo total puesto en bodega</dt><dd><x-cifra :value="$totals['total']" /></dd></div>
                </dl>
            </article>
        </div>

        {{-- ESCRITORIO: tabla real con <thead>, <tbody> y <tfoot> para los totales --}}
        <div class="mt-4 hidden md:block">
            <x-table caption="Liquidación por producto, en dólares">
                <thead>
                <tr>
                    <th scope="col">Producto</th>
                    <th scope="col" class="num">Cant.</th>
                    <th scope="col" class="num">FOB</th>
                    <th scope="col" class="num">CIF</th>
                    <th scope="col" class="num">Ad Valorem</th>
                    <th scope="col" class="num">FODINFA</th>
                    <th scope="col" class="num">IVA</th>
                    <th scope="col" class="num">G. locales</th>
                    <th scope="col" class="num">Costo total</th>
                    <th scope="col" class="num">C. unitario</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($liquidation['items'] as $row)
                    <tr>
                        <td>
                            <p class="font-medium">{{ $row['description'] }}</p>
                            <p class="font-mono text-xs text-texto-suave">{{ $row['tariff_code'] }} · AV {{ Money::formatPercent($row['ad_valorem_rate']) }}</p>
                        </td>
                        <td class="num">{{ $row['quantity'] }}</td>
                        <td class="num">{{ Money::format($row['fob']) }}</td>
                        <td class="num">{{ Money::format($row['cif']) }}</td>
                        <td class="num">{{ Money::format($row['ad_valorem']) }}</td>
                        <td class="num">{{ Money::format($row['fodinfa']) }}</td>
                        <td class="num">{{ Money::format($row['iva']) }}</td>
                        <td class="num">{{ Money::format($row['local_costs']) }}</td>
                        <td class="num">{{ Money::format($row['total']) }}</td>
                        <td class="num font-semibold">{{ Money::format($row['unit_cost']) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th scope="row">Totales</th>
                    <td></td>
                    <td class="num">{{ Money::format($totals['fob']) }}</td>
                    <td class="num">{{ Money::format($totals['cif']) }}</td>
                    <td class="num">{{ Money::format($totals['ad_valorem']) }}</td>
                    <td class="num">{{ Money::format($totals['fodinfa']) }}</td>
                    <td class="num">{{ Money::format($totals['iva']) }}</td>
                    <td class="num">{{ Money::format($totals['local_costs']) }}</td>
                    <td class="num">{{ Money::format($totals['total']) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row" colspan="8" class="num">Tributos pagados (Ad Valorem + FODINFA + IVA)</th>
                    <td class="num">{{ Money::format($totals['taxes']) }}</td>
                    <td></td>
                </tr>
                </tfoot>
            </x-table>
        </div>
    </section>

    {{-- Formularios y bloques: una columna en móvil, dos con md: en escritorio --}}
    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-panel class="self-start">
            <h2 class="border-b border-borde px-4 py-2 text-sm font-semibold text-primario">Datos generales</h2>
            <dl class="px-4 py-2 text-sm">
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Cliente</dt><dd class="text-right">{{ $import->client->company_name }}</dd></div>
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">RUC</dt><dd class="font-mono tabular-nums">{{ $import->client->ruc }}</dd></div>
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Operador</dt><dd>{{ $import->operator->full_name }}</dd></div>
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Transporte</dt><dd>{{ $import->transport_mode->label() }} · {{ $import->incoterm }}</dd></div>
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Embarque</dt><dd class="font-mono tabular-nums">{{ $import->shipped_at?->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Liberación estimada</dt><dd class="font-mono tabular-nums">{{ $import->estimated_release_at?->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 py-1"><dt class="text-texto-suave">Liberación real</dt><dd class="font-mono tabular-nums">{{ $import->released_at?->format('d/m/Y') ?? '—' }}</dd></div>
            </dl>
        </x-panel>

        <x-panel class="self-start">
            <h2 class="border-b border-borde px-4 py-2 text-sm font-semibold text-primario">Historial de eventos</h2>
            {{-- Lista ordenada: los eventos tienen una secuencia en el tiempo --}}
            <ol class="px-4 text-sm">
                @forelse ($import->events as $event)
                    <li class="border-b border-borde py-2 last:border-0">
                        <p class="flex flex-wrap justify-between gap-x-3">
                            <span class="font-medium">{{ $event->status->label() }}</span>
                            <time class="font-mono text-xs tabular-nums text-texto-suave" datetime="{{ $event->event_date->toIso8601String() }}">{{ $event->event_date->format('d/m/Y H:i') }}</time>
                        </p>
                        <p class="text-xs text-texto-suave">{{ $event->location }} · por {{ $event->user->full_name }}</p>
                        @if ($event->note)
                            <p class="mt-1 text-xs">{{ $event->note }}</p>
                        @endif
                    </li>
                @empty
                    <li class="py-3 text-texto-suave">Sin eventos registrados.</li>
                @endforelse
            </ol>
        </x-panel>
    </div>
@endsection
