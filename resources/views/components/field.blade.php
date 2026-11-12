@props(['name', 'label', 'required' => false, 'hint' => null, 'hintId' => null])

{{--
    Campo de formulario: etiqueta + control + ayuda + error.
    - <label for> vinculado al control: al tocar la etiqueta se enfoca el campo.
    - El mensaje de error del servidor aparece junto al campo que falló.
--}}
<div {{ $attributes->class('min-w-0') }}>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-texto">
        {{ $label }}
        @if ($required)<span class="text-peligro" data-required-mark>*</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p @if($hintId) id="{{ $hintId }}" @endif class="mt-1 text-xs text-texto-suave">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-peligro">{{ $message }}</p>
    @enderror
</div>
