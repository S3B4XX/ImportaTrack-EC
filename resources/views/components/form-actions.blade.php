@props(['cancel', 'submit'])

{{--
    Botones al pie de un formulario. En móvil van apilados y el principal queda arriba
    (flex-col-reverse); desde sm: van en fila, alineados a la derecha.
--}}
<div {{ $attributes->class('flex flex-col-reverse gap-2 sm:flex-row sm:justify-end md:col-span-2') }}>
    <x-button variant="secundario" :href="$cancel">Cancelar</x-button>
    <x-button>{{ $submit }}</x-button>
</div>
