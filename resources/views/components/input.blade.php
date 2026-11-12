@props(['name', 'type' => 'text'])

{{--
    Control de formulario: <input>, <select> (type="select") o <textarea> (type="textarea").
    - El id es igual al name para que el <label for> de x-field quede vinculado.
    - min-h-11 = 44 px de alto en móvil.
    - Si el servidor rechazó el campo, el borde cambia y x-field muestra el mensaje al lado.
--}}
@php
    $classes = [
        'block min-h-11 w-full rounded-md border bg-superficie px-3 text-sm text-texto placeholder:text-slate-400',
        'focus:border-primario focus:ring-2 focus:ring-primario/20 focus:outline-none',
        'disabled:cursor-not-allowed disabled:bg-fondo disabled:text-texto-suave',
        'border-peligro' => $errors->has($name),
        'border-slate-300' => ! $errors->has($name),
    ];
@endphp

@if ($type === 'select')
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->class($classes) }}>{{ $slot }}</select>
@elseif ($type === 'textarea')
    <textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes->class([...$classes, 'py-2']) }}>{{ $slot }}</textarea>
@else
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->class($classes) }}>
@endif
