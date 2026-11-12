<?php

namespace App\Http\Requests\Admin;

use App\Enums\ImportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Valida los filtros del listado de importaciones (llegan por la URL, con GET). */
class ImportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(ImportStatus::values())],
            'client' => ['nullable', 'integer'],
            'supplier' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return ['status' => 'estado', 'client' => 'cliente', 'supplier' => 'proveedor'];
    }
}
