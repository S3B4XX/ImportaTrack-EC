<?php

namespace App\Http\Requests\Admin;

use App\Models\TariffChapter;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Partida arancelaria. La sección y el capítulo vienen de dropdowns en cascada,
 * y el servidor vuelve a comprobar que el capítulo pertenezca a la sección
 * y que el código empiece con los 2 dígitos del capítulo.
 */
class TariffItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Acepta "8471.50.00.00" o "8471500000" y guarda solo dígitos.
        // Solo se limpia si llegó texto; un arreglo (code[]=...) lo rechaza la regla 'string'.
        $code = $this->input('code');

        $this->merge(['code' => is_string($code) ? preg_replace('/[\s.]/', '', $code) : $code]);
    }

    public function rules(): array
    {
        return [
            'tariff_section_id' => ['required', 'integer', Rule::exists('tariff_sections', 'id')],
            'tariff_chapter_id' => [
                'required',
                'integer',
                Rule::exists('tariff_chapters', 'id')->where('tariff_section_id', $this->integer('tariff_section_id')),
            ],
            'code' => [
                'bail',
                'required',
                'string',
                'digits:10',
                Rule::unique('tariff_items', 'code')->ignore($this->route('tariffItem')),
                function (string $attribute, mixed $value, Closure $fail) {
                    $chapter = TariffChapter::find($this->integer('tariff_chapter_id'));
                    if ($chapter && ! str_starts_with((string) $value, $chapter->code)) {
                        $fail("El código debe empezar con {$chapter->code}, que es el capítulo seleccionado.");
                    }
                },
            ],
            'description' => ['required', 'string', 'max:255'],
            'ad_valorem_rate' => ['required', 'numeric', 'decimal:0,2', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tariff_chapter_id.exists' => 'El capítulo seleccionado no pertenece a la sección elegida.',
            'code.digits' => 'El código de la subpartida debe tener 10 dígitos (ej. 8471.50.00.00).',
            'code.unique' => 'Ya existe una partida con este código.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tariff_section_id' => 'sección',
            'tariff_chapter_id' => 'capítulo',
            'code' => 'código',
            'description' => 'descripción',
            'ad_valorem_rate' => '% Ad Valorem',
        ];
    }
}
