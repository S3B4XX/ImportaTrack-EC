@extends('layouts.guest', ['title' => 'Iniciar sesión'])

@section('content')
    {{-- Misma barra sólida del color institucional que usa el resto del sistema --}}
    <x-panel class="overflow-hidden">
        <header class="bg-primario px-5 py-4 text-white">
            <p class="font-semibold tracking-tight">ImportaTrack EC</p>
            <p class="text-xs text-white/80">Gestión de importaciones</p>
        </header>

        <div class="p-5">
            <h1 class="text-lg font-semibold text-primario">Iniciar sesión</h1>
            <p class="mt-1 text-sm text-texto-suave">Accede con tu cuenta de la agencia.</p>

            {{-- novalidate: la validación la hace el servidor (LoginRequest) --}}
            <form method="POST" action="{{ route('login.store') }}" class="mt-5 space-y-4" novalidate>
                @csrf
                <x-field name="email" label="Correo">
                    <x-input name="email" type="email" value="{{ old('email') }}" autocomplete="username" autofocus />
                </x-field>

                <x-field name="password" label="Contraseña">
                    <x-input name="password" type="password" autocomplete="current-password" />
                </x-field>

                <label for="remember" class="flex min-h-11 items-center gap-2 text-sm">
                    <input id="remember" type="checkbox" name="remember" class="size-4 accent-primario"> Mantener la sesión iniciada
                </label>

                <x-button class="w-full">Ingresar</x-button>
            </form>
        </div>
    </x-panel>

    @if (config('app.show_demo_credentials'))
        <aside class="mt-4 rounded-md border border-borde bg-superficie px-4 py-3 text-xs">
            <p class="font-semibold text-primario">Usuario de demostración</p>
            <p class="mt-1">Administrador: <span class="font-mono">admin@importatrack.ec</span></p>
            <p>Contraseña: <span class="font-mono">ImportaTrack2026</span></p>
        </aside>
    @endif
@endsection
