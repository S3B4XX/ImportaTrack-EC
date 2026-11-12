@props(['value'])

@use('App\Support\Money')

{{--
    Monto en dólares. IBM Plex Mono + tabular-nums: todos los dígitos ocupan el mismo ancho,
    así las cifras quedan alineadas cuando se leen una debajo de otra.
--}}
<span {{ $attributes->class('font-mono tabular-nums whitespace-nowrap') }}>{{ Money::format($value) }}</span>
