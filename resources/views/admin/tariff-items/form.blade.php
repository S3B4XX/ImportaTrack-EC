@extends('layouts.app', ['title' => $item->exists ? 'Editar partida' : 'Nueva partida'])

@section('content')
    <x-page-header :title="$item->exists ? 'Editar partida' : 'Nueva partida arancelaria'"
                   subtitle="Elige la sección y se cargan solo sus capítulos. El código debe empezar con los 2 dígitos del capítulo." />

    <x-panel as="form" method="POST" novalidate
          action="{{ $item->exists ? route('admin.tariff-items.update', $item) : route('admin.tariff-items.store') }}"
          class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        {{-- Dropdown en cascada: Sección → Capítulo (los capítulos llegan en JSON desde TariffChapterController) --}}
        <x-field name="tariff_section_id" label="Sección del arancel" required>
            <x-input type="select" name="tariff_section_id" data-cascade-target="tariff_chapter_id" data-cascade-url="{{ route('admin.tariff-sections.chapters', '__ID__') }}">
                <option value="">Selecciona una sección</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected($selectedSection == $section->id)>
                        {{ $section->code }} — {{ $section->name }}
                    </option>
                @endforeach
            </x-input>
        </x-field>

        <x-field name="tariff_chapter_id" label="Capítulo" required>
            <x-input type="select" name="tariff_chapter_id" data-code-hint="code-hint" :disabled="$chapters->isEmpty()">
                @if ($chapters->isEmpty())
                    <option value="">Primero elige una sección</option>
                @else
                    <option value="">Selecciona un capítulo</option>
                    @foreach ($chapters as $chapter)
                        <option value="{{ $chapter->id }}" data-code="{{ $chapter->code }}"
                            @selected(old('tariff_chapter_id', $item->tariff_chapter_id) == $chapter->id)>
                            {{ $chapter->code }} — {{ $chapter->name }}
                        </option>
                    @endforeach
                @endif
            </x-input>
        </x-field>

        <x-field name="code" label="Código de la subpartida" required hint-id="code-hint"
                 hint="10 dígitos, con o sin puntos. Ej. 8471.50.00.00">
            <x-input name="code" value="{{ old('code', $item->exists ? $item->formatted_code : '') }}" inputmode="numeric" autocomplete="off" placeholder="0000.00.00.00" class="font-mono" />
        </x-field>

        <x-field name="ad_valorem_rate" label="% Ad Valorem" required hint="Entre 0 y 100, hasta 2 decimales.">
            <x-input name="ad_valorem_rate" value="{{ old('ad_valorem_rate', $item->ad_valorem_rate) }}" inputmode="decimal" />
        </x-field>

        <x-field name="description" label="Descripción" required class="md:col-span-2">
            <x-input type="textarea" name="description" rows="3" class="py-2">{{ old('description', $item->description) }}</x-input>
        </x-field>

        <x-form-actions :cancel="route('admin.tariff-items.index')" :submit="$item->exists ? 'Guardar cambios' : 'Crear partida'" />
    </x-panel>
@endsection
