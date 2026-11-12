<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportFilterRequest;
use App\Models\Import;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\View\View;

/** El Administrador consulta todas las importaciones, con su liquidación y su historial. */
class ImportController extends Controller
{
    public function index(ImportFilterRequest $request): View
    {
        $imports = Import::with(['client', 'supplier', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('client'), fn ($q) => $q->where('client_id', $request->integer('client')))
            ->when($request->filled('supplier'), fn ($q) => $q->where('supplier_id', $request->integer('supplier')))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.imports.index', [
            'imports' => $imports,
            'statuses' => ImportStatus::cases(),
            'clients' => User::where('role_id', Role::idFor(Role::CLIENT))->orderBy('company_name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function show(Import $import): View
    {
        $import->load(['client', 'operator', 'supplier', 'items.tariffItem', 'events.user']);

        $liquidation = $import->liquidation();

        return view('admin.imports.show', [
            'import' => $import,
            'liquidation' => $liquidation,
            'totals' => $liquidation['totals'],
            'deviation' => $import->calculateDeviation(),
            'compliance' => $import->getCompliance(),
        ]);
    }
}
