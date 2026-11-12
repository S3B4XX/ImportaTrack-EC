@props(['variant' => 'primario', 'href' => null])

{{--
    Botón de la aplicación. Si recibe href se dibuja como enlace <a>; si no, como <button>.
    - min-h-11 = 44 px: tamaño mínimo para tocarlo con el dedo en el celular.
    - focus-visible:ring: quien navega con teclado ve en qué botón está.
--}}
@php
    $base = 'inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-md px-4 text-sm font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-primario focus-visible:ring-offset-2';

    $variants = [
        'primario' => 'bg-primario text-white hover:bg-primario-oscuro',
        'secundario' => 'border border-slate-300 bg-superficie text-texto hover:bg-fondo',
        'peligro' => 'border border-peligro/40 bg-superficie text-peligro hover:bg-peligro-claro',
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$base, $variants[$variant]]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->class([$base, $variants[$variant]]) }}>{{ $slot }}</button>
@endif
