@extends('layouts.app', ['title' => 'Inicio'])

@section('content')
    <x-panel class="mx-auto max-w-xl p-5">
        <h1 class="text-lg font-semibold text-primario">Hola, {{ $user->first_name }}</h1>
        <p class="mt-2 text-sm text-texto-suave">
            Iniciaste sesión como <span class="font-medium text-texto">{{ $user->role->name }}</span>.
            El panel de este rol (registro y seguimiento de importaciones, liquidación y simulador)
            forma parte del Core, que se entrega en el Progreso 2.
        </p>
        <form method="POST" action="{{ route('logout') }}" class="mt-5">
            @csrf
            <x-button variant="secundario">Cerrar sesión</x-button>
        </form>
    </x-panel>
@endsection
