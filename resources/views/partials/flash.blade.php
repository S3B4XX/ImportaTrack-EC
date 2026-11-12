{{-- Mensajes del sistema. Empiezan con una palabra ("Listo", "Error"): no dependen solo del color. --}}
@if (session('success'))
    <p class="mb-5 rounded-md border border-exito/30 border-l-4 border-l-exito bg-exito-claro px-4 py-3 text-sm text-texto" role="status">
        <strong class="font-semibold text-exito">Listo.</strong> {{ session('success') }}
    </p>
@endif

@if (session('error'))
    <p class="mb-5 rounded-md border border-peligro/30 border-l-4 border-l-peligro bg-peligro-claro px-4 py-3 text-sm text-texto" role="alert">
        <strong class="font-semibold text-peligro">Error.</strong> {{ session('error') }}
    </p>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-md border border-peligro/30 border-l-4 border-l-peligro bg-peligro-claro px-4 py-3 text-sm text-texto" role="alert">
        <p class="font-semibold text-peligro">El servidor rechazó el formulario. Revisa {{ $errors->count() === 1 ? 'el campo marcado' : 'los '.$errors->count().' campos marcados' }}:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
