<?php

namespace Tests\Feature;

use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Las tasas se validan en el servidor con TaxRateRequest antes de guardarse. */
class TaxRateTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('email', 'admin@importatrack.ec')->first();
    }

    public function test_el_administrador_actualiza_una_tasa(): void
    {
        $iva = TaxRate::where('code', TaxRate::IVA)->first();

        $this->actingAs($this->admin())
            ->put(route('admin.tax-rates.update', $iva), [
                'name' => $iva->name,
                'rate' => '12',
                'valid_from' => '2026-01-01',
            ])
            ->assertRedirect(route('admin.tax-rates.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(12.0, TaxRate::rateFor(TaxRate::IVA));
    }

    public function test_rechaza_una_tasa_fuera_de_rango(): void
    {
        $iva = TaxRate::where('code', TaxRate::IVA)->first();

        foreach (['-5', '150', 'quince'] as $rate) {
            $this->actingAs($this->admin())
                ->put(route('admin.tax-rates.update', $iva), [
                    'name' => $iva->name,
                    'rate' => $rate,
                    'valid_from' => '2026-01-01',
                ])
                ->assertSessionHasErrors('rate');
        }

        $this->assertSame(15.0, TaxRate::rateFor(TaxRate::IVA));
    }
}
