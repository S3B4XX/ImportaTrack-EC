@props(['caption'])

{{--
    Tabla de datos con borde de 1 px en lugar de sombra (diseño plano).
    - <caption> describe la tabla a los lectores de pantalla (sr-only: no se ve).
    - [&_th] y [&_td] aplican el estilo a todas las celdas desde aquí,
      así las vistas no repiten las mismas clases en cada <th> y <td>.
    - Las celdas con cifras se marcan con class="num": van a la derecha, en IBM Plex Mono
      y con tabular-nums (todos los dígitos del mismo ancho), para que las unidades,
      decenas y centavos queden alineados en columna y se puedan comparar de un vistazo.
    - <tfoot> lleva un borde superior más fuerte: separa los totales del detalle.
    - overflow-x-auto solo actúa si una tabla ancha no cabe en la pantalla.
--}}
<div class="overflow-x-auto rounded-md border border-borde bg-superficie">
    <table {{ $attributes->class([
        'min-w-full text-sm',
        '[&_th]:px-3 [&_th]:py-2 [&_th]:text-left [&_th]:text-xs [&_th]:font-semibold [&_th]:text-texto-suave',
        '[&_td]:px-3 [&_td]:py-2',
        '[&_.num]:text-right [&_td.num]:font-mono [&_td.num]:tabular-nums [&_td.num]:whitespace-nowrap',
        '[&_thead]:border-b [&_thead]:border-borde [&_thead]:bg-fondo',
        '[&_tbody_tr]:border-b [&_tbody_tr]:border-borde [&_tbody_tr:last-child]:border-0',
        '[&_tfoot]:border-t-2 [&_tfoot]:border-primario [&_tfoot]:font-semibold [&_tfoot_tr+tr]:border-t [&_tfoot_tr+tr]:border-borde',
    ]) }}>
        <caption class="sr-only">{{ $caption }}</caption>
        {{ $slot }}
    </table>
</div>
