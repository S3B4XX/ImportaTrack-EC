<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $suppliers = Supplier::withCount('imports')
            ->when($request->filled('country'), fn ($q) => $q->where('country', $request->query('country')))
            ->when($search !== '', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.suppliers.index', [
            'suppliers' => $suppliers,
            'countries' => config('catalogs.countries'),
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.form', [
            'supplier' => new Supplier,
            'countries' => config('catalogs.countries'),
        ]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Proveedor {$supplier->name} creado correctamente.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.form', [
            'supplier' => $supplier,
            'countries' => config('catalogs.countries'),
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Proveedor {$supplier->name} actualizado.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->imports()->exists()) {
            return back()->with('error', "No se puede eliminar {$supplier->name}: tiene importaciones asociadas.");
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Proveedor {$supplier->name} eliminado.");
    }
}
