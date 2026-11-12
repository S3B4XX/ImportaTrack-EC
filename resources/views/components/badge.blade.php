@props(['tone' => 'neutro'])

{{--
    Etiqueta de estado. El color solo refuerza: el significado siempre está en el texto,
    para que también lo entienda quien no distingue colores o usa lector de pantalla.
--}}
@php
    $tones = [
        'neutro' => 'border-borde bg-fondo text-texto-suave',
        'primario' => 'border-primario/20 bg-primario-claro text-primario',
        'exito' => 'border-exito/30 bg-exito-claro text-exito',
        'alerta' => 'border-alerta/30 bg-alerta-claro text-alerta',
        'peligro' => 'border-peligro/30 bg-peligro-claro text-peligro',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-sm border px-2 py-0.5 text-xs font-medium whitespace-nowrap', $tones[$tone]]) }}>{{ $slot }}</span>
