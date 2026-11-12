@props(['as' => 'section'])

{{--
    Bloque de contenido: superficie blanca con borde de 1 px, sin sombra (diseño plano).
    "as" permite elegir la etiqueta semántica correcta: section, article o form.
--}}
<{{ $as }} {{ $attributes->class('rounded-md border border-borde bg-superficie') }}>
    {{ $slot }}
</{{ $as }}>
