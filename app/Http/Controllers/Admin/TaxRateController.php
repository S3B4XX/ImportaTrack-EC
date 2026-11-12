<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaxRateRequest;
use App\Models\TaxRate;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Configuración de las tasas (IVA y FODINFA) que usa la liquidación del Core. */
class TaxRateController extends Controller
{
    public function index(): View
    {
        return view('admin.tax-rates.index', [
            'taxRates' => TaxRate::orderBy('code', 'desc')->get(),
        ]);
    }

    public function edit(TaxRate $taxRate): View
    {
        return view('admin.tax-rates.form', ['taxRate' => $taxRate]);
    }

    public function update(TaxRateRequest $request, TaxRate $taxRate): RedirectResponse
    {
        $taxRate->update($request->validated());

        return redirect()->route('admin.tax-rates.index')
            ->with('success', "Tasa {$taxRate->code} actualizada a ".Money::formatPercent($taxRate->rate).'. Las liquidaciones ya usan el nuevo valor.');
    }
}
