<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TariffChapterController;
use App\Http\Controllers\Admin\TariffItemController;
use App\Http\Controllers\Admin\TaxRateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Redirige a cada usuario a su área según el rol
Route::get('/', HomeController::class)->middleware('auth')->name('home');

// ─── Administración ───────────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:'.Role::ADMIN])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('users', UserController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
        Route::resource('tariff-items', TariffItemController::class)->except('show')
            ->parameters(['tariff-items' => 'tariffItem']);

        // Dropdown en cascada: capítulos de una sección del arancel (JSON)
        Route::get('tariff-sections/{section}/chapters', [TariffChapterController::class, 'bySection'])
            ->name('tariff-sections.chapters');

        Route::get('tax-rates', [TaxRateController::class, 'index'])->name('tax-rates.index');
        Route::get('tax-rates/{taxRate}/edit', [TaxRateController::class, 'edit'])->name('tax-rates.edit');
        Route::put('tax-rates/{taxRate}', [TaxRateController::class, 'update'])->name('tax-rates.update');

        Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
        Route::get('imports/{import}', [ImportController::class, 'show'])->name('imports.show');
    });
