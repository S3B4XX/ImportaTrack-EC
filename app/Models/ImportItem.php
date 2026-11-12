<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Producto de una importación. Calcula sus propios tributos según su partida. */
class ImportItem extends Model
{
    protected $fillable = ['import_id', 'tariff_item_id', 'description', 'quantity', 'unit_fob'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_fob' => 'decimal:2',
        ];
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function tariffItem(): BelongsTo
    {
        return $this->belongsTo(TariffItem::class);
    }

    /** FOB del producto = cantidad × FOB unitario. */
    public function fobTotal(): float
    {
        return Money::fromCents($this->quantity * Money::toCents($this->unit_fob));
    }

    /**
     * CIF del producto = FOB + la parte del flete y del seguro que le corresponde.
     * Esa parte la calcula Import::liquidation() según el peso del FOB del producto.
     */
    public function calculateCif(float $freightAndInsurance): float
    {
        return round($this->fobTotal() + $freightAndInsurance, 2);
    }

    /** Ad Valorem = % de la partida × CIF. */
    public function calculateAdValorem(float $cif): float
    {
        return Money::percent($cif, (float) $this->tariffItem->ad_valorem_rate);
    }

    /** FODINFA = tasa FODINFA (0,5%) × CIF. */
    public function calculateFodinfa(float $cif, float $fodinfaRate): float
    {
        return Money::percent($cif, $fodinfaRate);
    }

    /** IVA = tasa IVA (15%) × (CIF + Ad Valorem + FODINFA). */
    public function calculateIva(float $cif, float $adValorem, float $fodinfa, float $ivaRate): float
    {
        return Money::percent($cif + $adValorem + $fodinfa, $ivaRate);
    }

    /**
     * Liquidación completa del producto (pasos 2 a 5 de la especificación).
     * Recibe su parte del flete + seguro y de los gastos locales, ya repartida por Import.
     *
     * @return array<string, float|int|string>
     */
    public function liquidate(float $freightAndInsurance, float $localCosts, float $ivaRate, float $fodinfaRate): array
    {
        $fob = $this->fobTotal();
        $cif = $this->calculateCif($freightAndInsurance);
        $adValorem = $this->calculateAdValorem($cif);
        $fodinfa = $this->calculateFodinfa($cif, $fodinfaRate);
        $iva = $this->calculateIva($cif, $adValorem, $fodinfa, $ivaRate);
        $total = round($cif + $adValorem + $fodinfa + $iva + $localCosts, 2);

        return [
            'description' => $this->description,
            'tariff_code' => $this->tariffItem->formatted_code,
            'ad_valorem_rate' => (float) $this->tariffItem->ad_valorem_rate,
            'quantity' => $this->quantity,
            'fob' => $fob,
            'cif' => $cif,
            'ad_valorem' => $adValorem,
            'fodinfa' => $fodinfa,
            'iva' => $iva,
            'taxes' => round($adValorem + $fodinfa + $iva, 2),
            'local_costs' => $localCosts,
            'total' => $total,
            'unit_cost' => $this->quantity > 0 ? round($total / $this->quantity, 2) : 0.0,
        ];
    }
}
