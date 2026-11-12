@props(['title', 'subtitle' => null])

{{-- Encabezado de cada pantalla: contiene el único <h1> de la página. --}}
<header class="mb-5 flex flex-col gap-3 border-b border-borde pb-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-primario md:text-2xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-texto-suave">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-2">{{ $actions }}</div>
    @endisset
</header>
