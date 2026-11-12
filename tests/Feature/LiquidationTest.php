<?php

namespace Tests\Feature;

use App\Enums\Compliance;
use App\Models\Import;
use App\Models\TariffItem;
use App\Models\TaxRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/** Reproduce el ejemplo de la sección 2 del Documento de Especificación Funcional. */
class LiquidationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_la_liquidacion_coincide_con_el_ejemplo_de_la_especificacion(): void
    {
        $import = Import::where('code', 'IMP-2026-0001')->firstOrFail();
        $liquidation = $import->liquidation();

        [$servers, $disks] = $liquidation['items'];

        $this->assertSame(8720.00, $servers['cif']);
        $this->assertSame(436.00, $servers['ad_valorem']);
        $this->assertSame(43.60, $servers['fodinfa']);
        $this->assertSame(1379.94, $servers['iva']);
        $this->assertSame(240.00, $servers['local_costs']);
        $this->assertSame(1081.95, $servers['unit_cost']);

        $this->assertSame(2180.00, $disks['cif']);
        $this->assertSame(361.34, $disks['iva']);
        $this->assertSame(56.60, $disks['unit_cost']);

        $this->assertSame(10900.00, $import->calculateCif());
        $this->assertSame(13649.78, $import->calculateLandedCost());
        $this->assertEqualsWithDelta(0.365, $liquidation['totals']['over_fob'], 0.001);
    }

    public function test_el_indicador_de_cumplimiento_marca_retraso_leve(): void
    {
        // Estimada en 30 días, liberada en 34 → desviación 13,3%
        $import = Import::where('code', 'IMP-2026-0001')->firstOrFail();

        $this->assertEqualsWithDelta(0.1333, $import->calculateDeviation(), 0.0001);
        $this->assertSame(Compliance::RetrasoLeve, $import->getCompliance());
    }

    public function test_la_liquidacion_usa_la_tasa_que_configura_el_administrador(): void
    {
        TaxRate::where('code', TaxRate::IVA)->update(['rate' => 12]);

        $import = Import::where('code', 'IMP-2026-0001')->firstOrFail();

        $this->assertSame(13301.52, $import->calculateLandedCost());
    }

    public function test_la_suma_por_producto_cuadra_con_la_cabecera(): void
    {
        // 3 productos iguales con flete 100 y gastos locales 100: 100 ÷ 3 no es exacto,
        // pero el reparto no debe perder ningún centavo.
        $base = Import::where('code', 'IMP-2026-0001')->firstOrFail();

        $import = Import::create([
            'code' => 'IMP-PRUEBA-01',
            'client_id' => $base->client_id,
            'operator_id' => $base->operator_id,
            'supplier_id' => $base->supplier_id,
            'transport_mode' => 'MARITIMO',
            'incoterm' => 'FOB',
            'freight' => 100,
            'insurance' => 0,
            'local_costs' => 100,
        ]);

        foreach (['A', 'B', 'C'] as $name) {
            $import->items()->create([
                'tariff_item_id' => TariffItem::where('code', '8471500000')->value('id'),
                'description' => "Producto {$name}",
                'quantity' => 1,
                'unit_fob' => 100,
            ]);
        }

        $totals = $import->liquidation()['totals'];

        $this->assertSame(400.00, $import->calculateCif());
        $this->assertSame(400.00, $totals['cif']);
        $this->assertSame(100.00, $totals['local_costs']);
    }

    public function test_sin_la_tasa_configurada_no_se_liquida(): void
    {
        TaxRate::where('code', TaxRate::IVA)->delete();

        $this->expectException(RuntimeException::class);

        Import::where('code', 'IMP-2026-0001')->firstOrFail()->liquidation();
    }
}
