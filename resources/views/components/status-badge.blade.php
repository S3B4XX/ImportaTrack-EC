@props(['status'])

{{--
    Estado de la importación. Verde, ámbar y rojo quedan reservados para el semáforo de
    cumplimiento; aquí solo se distingue: sin iniciar (neutro), en curso (primario) y concluida.
--}}
@php
    $tones = [
        'REGISTRADA' => 'neutro',
        'EMBARCADA' => 'primario',
        'EN_TRANSITO' => 'primario',
        'ARRIBO' => 'primario',
        'EN_AFORO' => 'primario',
        'LIBERADA' => 'exito',
        'ENTREGADA' => 'exito',
    ];
@endphp

<x-badge :tone="$tones[$status->value]">{{ $status->label() }}</x-badge>
