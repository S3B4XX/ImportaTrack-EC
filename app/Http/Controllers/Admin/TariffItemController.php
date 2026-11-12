<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TariffItemRequest;
use App\Models\TariffChapter;
use App\Models\TariffItem;
use App\Models\TariffSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TariffItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = TariffItem::with('chapter.section')
            ->search($request->query('q'))
            ->when($request->filled('section'), fn ($q) => $q->whereHas(
                'chapter', fn ($c) => $c->where('tariff_section_id', $request->integer('section'))
            ))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tariff-items.index', [
            'items' => $items,
            'sections' => TariffSection::orderBy('id')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return $this->form(new TariffItem, $request->old('tariff_section_id'));
    }

    public function store(TariffItemRequest $request): RedirectResponse
    {
        $item = TariffItem::create($request->safe()->except('tariff_section_id'));

        return redirect()->route('admin.tariff-items.index')
            ->with('success', "Partida {$item->formatted_code} creada correctamente.");
    }

    public function edit(Request $request, TariffItem $tariffItem): View
    {
        $tariffItem->load('chapter');

        return $this->form($tariffItem, $request->old('tariff_section_id', $tariffItem->chapter?->tariff_section_id));
    }

    public function update(TariffItemRequest $request, TariffItem $tariffItem): RedirectResponse
    {
        $tariffItem->update($request->safe()->except('tariff_section_id'));

        return redirect()->route('admin.tariff-items.index')
            ->with('success', "Partida {$tariffItem->formatted_code} actualizada.");
    }

    public function destroy(TariffItem $tariffItem): RedirectResponse
    {
        if ($tariffItem->importItems()->exists()) {
            return back()->with('error', "No se puede eliminar la partida {$tariffItem->formatted_code}: está usada en importaciones.");
        }

        $tariffItem->delete();

        return redirect()->route('admin.tariff-items.index')
            ->with('success', "Partida {$tariffItem->formatted_code} eliminada.");
    }

    /**
     * El formulario recibe los capítulos de la sección ya elegida (al editar o al volver
     * con errores de validación), así el dropdown funciona aunque el JS no cargue.
     */
    private function form(TariffItem $item, mixed $sectionId): View
    {
        return view('admin.tariff-items.form', [
            'item' => $item,
            'sections' => TariffSection::orderBy('id')->get(),
            'selectedSection' => $sectionId,
            'chapters' => $sectionId
                ? TariffChapter::where('tariff_section_id', $sectionId)->orderBy('code')->get()
                : collect(),
        ]);
    }
}
