@extends('layouts.app', ['title' => 'Proveedores'])

@section('content')
    <x-page-header title="Proveedores del exterior" subtitle="Empresas a las que los clientes compran la mercadería.">
        <x-slot:actions>
            <x-button :href="route('admin.suppliers.create')">+ Nuevo proveedor</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-panel as="form" method="GET" class="mb-4 grid grid-cols-1 gap-3 p-3 sm:grid-cols-[1fr_200px_auto]">
        {{-- sr-only: la etiqueta no se ve, pero los lectores de pantalla sí la leen --}}
        <label for="q" class="sr-only">Buscar proveedor</label>
        <x-input name="q" value="{{ request('q') }}" placeholder="Buscar por nombre" />
        <label for="country" class="sr-only">Filtrar por país</label>
        <x-input type="select" name="country">
            <option value="">Todos los países</option>
            @foreach ($countries as $country)
                <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
            @endforeach
        </x-input>
        <x-button variant="secundario">Filtrar</x-button>
    </x-panel>

    @if ($suppliers->isEmpty())
        <x-empty>No se encontraron proveedores.</x-empty>
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($suppliers as $supplier)
                <x-panel as="article" class="p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold">{{ $supplier->name }}</p>
                            <p class="text-sm text-texto-suave">{{ $supplier->country }}</p>
                        </div>
                        <x-badge>{{ $supplier->imports_count }} imp.</x-badge>
                    </div>
                    <p class="mt-2 truncate text-sm text-texto-suave">{{ $supplier->email }} {{ $supplier->phone ? '· '.$supplier->phone : '' }}</p>
                    <div class="mt-3 flex gap-2">
                        <x-button variant="secundario" :href="route('admin.suppliers.edit', $supplier)" class="flex-1">Editar</x-button>
                        <x-delete-button :action="route('admin.suppliers.destroy', $supplier)" :name="$supplier->name" />
                    </div>
                </x-panel>
            @endforeach
        </div>

        <div class="hidden md:block">
            <x-table caption="Proveedores del exterior">
                <thead>
                <tr>
                    <th scope="col">Proveedor</th>
                    <th scope="col">País</th>
                    <th scope="col">Contacto</th>
                    <th scope="col" class="num">Importaciones</th>
                    <th scope="col" class="num">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($suppliers as $supplier)
                    <tr>
                        <td class="font-medium">{{ $supplier->name }}</td>
                        <td>{{ $supplier->country }}</td>
                        <td>
                            <p>{{ $supplier->email ?? '—' }}</p>
                            <p class="text-xs text-texto-suave">{{ $supplier->phone }}</p>
                        </td>
                        <td class="num">{{ $supplier->imports_count }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <x-button variant="secundario" :href="route('admin.suppliers.edit', $supplier)">Editar</x-button>
                                <x-delete-button :action="route('admin.suppliers.destroy', $supplier)" :name="$supplier->name" />
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-table>
        </div>

        <div class="mt-4">{{ $suppliers->links() }}</div>
    @endif
@endsection
