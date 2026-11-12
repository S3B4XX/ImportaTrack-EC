<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImportStatus;
use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\TariffItem;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $clientRole = Role::idFor(Role::CLIENT);

        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Usuarios', 'value' => User::count(), 'hint' => User::where('role_id', $clientRole)->count().' clientes', 'route' => 'admin.users.index'],
                ['label' => 'Proveedores', 'value' => Supplier::count(), 'hint' => Supplier::distinct()->count('country').' países', 'route' => 'admin.suppliers.index'],
                ['label' => 'Partidas arancelarias', 'value' => TariffItem::count(), 'hint' => 'con % Ad Valorem', 'route' => 'admin.tariff-items.index'],
                ['label' => 'Importaciones', 'value' => Import::count(), 'hint' => Import::whereNotIn('status', [ImportStatus::Liberada, ImportStatus::Entregada])->count().' en curso', 'route' => 'admin.imports.index'],
            ],
            'taxRates' => TaxRate::orderBy('code', 'desc')->get(),
            'recentImports' => Import::with(['client', 'supplier'])->latest('updated_at')->take(5)->get(),
        ]);
    }
}
