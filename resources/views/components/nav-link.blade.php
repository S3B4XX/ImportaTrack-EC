@props(['href', 'active' => false])

{{-- Enlace del menú. aria-current avisa a los lectores de pantalla cuál es la página actual. --}}
<a href="{{ $href }}" @if ($active) aria-current="page" @endif
   {{ $attributes->class([
       'flex min-h-11 items-center px-3 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-primario',
       'bg-primario-claro font-semibold text-primario' => $active,
       'font-medium text-texto hover:bg-fondo' => ! $active,
   ]) }}>{{ $slot }}</a>
