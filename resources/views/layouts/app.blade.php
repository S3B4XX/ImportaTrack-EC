<!DOCTYPE html>
<html lang="es" class="h-full bg-fondo">
<head>
    @include('partials.head')
</head>
<body class="min-h-full font-sans text-texto antialiased">
@php
    // Opciones del menú de Administración: se definen una vez y se dibujan en móvil y en escritorio.
    $nav = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Inicio'],
        ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Usuarios'],
        ['route' => 'admin.suppliers.index', 'match' => 'admin.suppliers.*', 'label' => 'Proveedores'],
        ['route' => 'admin.tariff-items.index', 'match' => 'admin.tariff-items.*', 'label' => 'Partidas'],
        ['route' => 'admin.tax-rates.index', 'match' => 'admin.tax-rates.*', 'label' => 'Tasas'],
        ['route' => 'admin.imports.index', 'match' => 'admin.imports.*', 'label' => 'Importaciones'],
    ];
    $user = auth()->user();
@endphp

<header>
    {{-- Barra superior sólida en el color institucional: nombre del sistema y rol del usuario activo. --}}
    <div class="bg-primario text-white">
        <div class="mx-auto flex min-h-14 max-w-6xl items-center justify-between gap-3 px-4">
            <a href="{{ route('home') }}" class="flex min-h-11 items-center font-semibold tracking-tight focus:outline-none focus-visible:ring-2 focus-visible:ring-white">ImportaTrack EC</a>

            @if ($user)
                <div class="flex items-center gap-1">
                    {{-- <details> abre y cierra el menú sin JavaScript --}}
                    <details class="relative">
                        <summary class="flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-md px-2 text-sm hover:bg-primario-oscuro focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <span class="hidden sm:inline">{{ $user->full_name }}</span>
                            <span class="rounded-sm border border-white/40 px-2 py-0.5 text-xs font-medium">{{ $user->role->name }}</span>
                        </summary>
                        <div class="absolute right-0 z-40 mt-1 w-60 rounded-md border border-borde bg-superficie p-3 text-texto shadow-sm">
                            <p class="text-sm font-medium">{{ $user->full_name }}</p>
                            <p class="mb-3 truncate text-xs text-texto-suave">{{ $user->email }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-button variant="secundario" class="w-full">Cerrar sesión</x-button>
                            </form>
                        </div>
                    </details>

                    @if ($user->isAdmin())
                        {{-- Móvil y tablet: el menú va dentro de un desplegable --}}
                        <details class="relative lg:hidden">
                            <summary class="flex min-h-11 cursor-pointer list-none items-center rounded-md border border-white/40 px-3 text-sm font-medium hover:bg-primario-oscuro focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Menú</summary>
                            <nav class="absolute right-0 z-40 mt-1 w-56 rounded-md border border-borde bg-superficie py-1 shadow-sm" aria-label="Principal móvil">
                                @foreach ($nav as $item)
                                    <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['match'])">{{ $item['label'] }}</x-nav-link>
                                @endforeach
                            </nav>
                        </details>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if ($user?->isAdmin())
        {{-- Escritorio: el menú es una fila bajo la barra --}}
        <nav class="hidden border-b border-borde bg-superficie lg:block" aria-label="Principal">
            <div class="mx-auto flex max-w-6xl px-4">
                @foreach ($nav as $item)
                    <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['match'])">{{ $item['label'] }}</x-nav-link>
                @endforeach
            </div>
        </nav>
    @endif
</header>

<main class="mx-auto max-w-6xl px-4 py-5 md:py-6">
    @include('partials.flash')
    @yield('content')
</main>

<footer class="mx-auto max-w-6xl px-4 pb-8 text-xs text-texto-suave">
    ImportaTrack EC · Ingeniería Web (ISWZ3101) · UDLA
</footer>
</body>
</html>
