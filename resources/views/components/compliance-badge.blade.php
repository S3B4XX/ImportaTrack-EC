@props(['compliance'])

{{--
    Semáforo de cumplimiento. Lleva un punto de color Y el texto ("Retraso leve"):
    el color nunca es la única señal (accesibilidad).
    Mientras la carga no se libera no hay desviación que mostrar.
--}}
@php
    $tones = ['A_TIEMPO' => 'exito', 'RETRASO_LEVE' => 'alerta', 'RETRASO_CRITICO' => 'peligro'];
@endphp

@if ($compliance)
    <x-badge :tone="$tones[$compliance->value]">
        <span class="size-2 rounded-full bg-current" aria-hidden="true"></span>
        {{ $compliance->label() }}
    </x-badge>
@else
    <x-badge>Pendiente</x-badge>
@endif
