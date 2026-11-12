@props(['action', 'name'])

{{-- Eliminar siempre va por POST con @method('DELETE') y pide confirmación antes de enviar. --}}
<form method="POST" action="{{ $action }}" onsubmit="return confirm('¿Eliminar {{ addslashes($name) }}? Esta acción no se puede deshacer.')">
    @csrf
    @method('DELETE')
    <x-button variant="peligro" {{ $attributes }} aria-label="Eliminar {{ $name }}">Eliminar</x-button>
</form>
