@extends('layouts.app', ['title' => 'Partidas arancelarias'])

@use('App\Support\Money')

@section('content')
    <x-page-header title="Partidas arancelarias" subtitle="Catálogo del arancel con el % Ad Valorem que usa la liquidación.">
        <x-slot:actions>
            <x-button :href="route('admin.tariff-items.create')">+ Nueva partida</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-panel as="form" method="GET" class="mb-4 grid grid-cols-1 gap-3 p-3 sm:grid-cols-[1fr_260px_auto]">
        {{-- sr-only: la etiqueta no se ve, pero los lectores de pantalla sí la leen --}}
        <label for="q" class="sr-only">Buscar partida</label>
        <x-input name="q" value="{{ request('q') }}" placeholder="Buscar por código (8471…) o descripción" />
        <label for="section" class="sr-only">Filtrar por sección</label>
        <x-input type="select" name="section">
            <option value="">Todas las secciones</option>
            @foreach ($sections as $section)
                <option value="{{ $section->id }}" @selected(request('section') == $section->id)>Sección {{ $section->code }} — {{ \Illuminate\Support\Str::limit($section->name, 30) }}</option>
            @endforeach
        </x-input>
        <x-button variant="secundario">Buscar</x-button>
    </x-panel>

    @if ($items->isEmpty())
        <x-empty>No se encontraron partidas.</x-empty>
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($items as $item)
                <x-panel as="article" class="p-3">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-mono font-semibold">{{ $item->formatted_code }}</p>
                        <x-badge tone="primario">AV {{ Money::formatPercent($item->ad_valorem_rate) }}</x-badge>
                    </div>
                    <p class="mt-1 text-sm">{{ $item->description }}</p>
                    <p class="mt-1 text-xs text-texto-suave">Sección {{ $item->chapter->section->code }} · Cap. {{ $item->chapter->code }}</p>
                    <div class="mt-3 flex gap-2">
                        <x-button variant="secundario" :href="route('admin.tariff-items.edit', $item)" class="flex-1">Editar</x-button>
                        <x-delete-button :action="route('admin.tariff-items.destroy', $item)" :name="'la partida '.$item->formatted_code" />
                    </div>
                </x-panel>
            @endforeach
        </div>

        <div class="hidden md:block">
            <x-table caption="Partidas arancelarias">
                <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Sección / Capítulo</th>
                    <th scope="col" class="num">Ad Valorem</th>
                    <th scope="col" class="num">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="font-mono font-medium whitespace-nowrap">{{ $item->formatted_code }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="whitespace-nowrap">
                            <p>Sección {{ $item->chapter->section->code }}</p>
                            <p class="text-xs text-texto-suave">Capítulo {{ $item->chapter->code }}</p>
                        </td>
                        <td class="num">{{ Money::formatPercent($item->ad_valorem_rate) }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <x-button variant="secundario" :href="route('admin.tariff-items.edit', $item)">Editar</x-button>
                                <x-delete-button :action="route('admin.tariff-items.destroy', $item)" :name="'la partida '.$item->formatted_code" />
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    @endif
@endsection
