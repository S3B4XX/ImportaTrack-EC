<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffSection;
use Illuminate\Http\JsonResponse;

class TariffChapterController extends Controller
{
    /**
     * Devuelve en JSON los capítulos de una sección del arancel.
     * Lo consume el dropdown en cascada (Sección → Capítulo) del formulario de partidas.
     */
    public function bySection(TariffSection $section): JsonResponse
    {
        return response()->json(
            $section->chapters()->get(['id', 'code', 'name'])
        );
    }
}
