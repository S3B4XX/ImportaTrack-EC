@extends('layouts.app', ['title' => 'Importaciones'])

@use('App\Support\Money')

@section('content')
    <x-page-header title="Importaciones"
                   subtitle="Consulta de todas las importaciones. El registro y seguimiento lo hace el Operador (Core)." />

    <x-panel as="form" method="GET" class="mb-4 grid grid-cols-1 gap-3 p-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]">
        {{-- sr-only: la etiqueta no se ve, pero los lectores de pantalla sí la leen --}}
        <label for="status" class="sr-only">Filtrar por estado</label>
        <x-input type="select" name="status">
            <option value="">Todos los estados</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </x-input>
        <label for="client" class="sr-only">Filtrar por cliente</label>
        <x-input type="select" name="client">
            <option value="">Todos los clientes</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected(request('client') == $client->id)>{{ $client->company_name ?? $client->full_name }}</option>
            @endforeach
        </x-input>
        <label for="supplier" class="sr-only">Filtrar por proveedor</label>
        <x-input type="select" name="supplier">
            <option value="">Todos los proveedores</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(request('supplier') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </x-input>
        <x-button variant="secundario">Filtrar</x-button>
    </x-panel>

    @if ($imports->isEmpty())
        <x-empty>No hay importaciones con esos filtros.</x-empty>
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($imports as $import)
                <x-panel as="article" class="p-3">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-mono font-semibold">{{ $import->code }}</h2>
                        <x-status-badge :status="$import->status" />
                    </div>
                    <p class="mt-1 text-sm">{{ $import->client->company_name }}</p>
                    <p class="text-xs text-texto-suave">{{ $import->supplier->name }} · {{ $import->transport_mode->label() }}</p>
                    <p class="mt-2 flex items-center justify-between gap-3 text-sm">
                        <span>FOB <x-cifra :value="$import->totalFob()" class="font-semibold" /></span>
                        <x-compliance-badge :compliance="$import->getCompliance()" />
                    </p>
                    <x-button variant="secundario" :href="route('admin.imports.show', $import)" class="mt-3 w-full">Ver liquidación</x-button>
                </x-panel>
            @endforeach
        </div>

        <div class="hidden md:block">
            <x-table caption="Importaciones registradas">
                <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Proveedor</th>
                    <th scope="col">Transporte</th>
                    <th scope="col" class="num">FOB</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Cumplimiento</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($imports as $import)
                    <tr>
                        <td><a href="{{ route('admin.imports.show', $import) }}" class="font-mono font-medium text-primario underline">{{ $import->code }}</a></td>
                        <td>{{ $import->client->company_name }}</td>
                        <td>{{ $import->supplier->name }}</td>
                        <td>{{ $import->transport_mode->label() }} · {{ $import->incoterm }}</td>
                        <td class="num">{{ Money::format($import->totalFob()) }}</td>
                        <td><x-status-badge :status="$import->status" /></td>
                        <td><x-compliance-badge :compliance="$import->getCompliance()" /></td>
                    </tr>
                @endforeach
                </tbody>
            </x-table>
        </div>

        <div class="mt-4">{{ $imports->links() }}</div>
    @endif
@endsection
