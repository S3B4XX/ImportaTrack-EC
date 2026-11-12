<?php

namespace App\Models;

use App\Enums\Compliance;
use App\Enums\CustomsChannel;
use App\Enums\ImportStatus;
use App\Enums\TransportMode;
use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Importación. Concentra la lógica del Core: CIF, tributos, costo puesto en bodega y desviación. */
class Import extends Model
{
    protected $fillable = [
        'code', 'client_id', 'operator_id', 'supplier_id', 'transport_mode', 'incoterm',
        'freight', 'insurance', 'local_costs', 'status', 'customs_channel',
        'shipped_at', 'estimated_release_at', 'released_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'transport_mode' => TransportMode::class,
            'status' => ImportStatus::class,
            'customs_channel' => CustomsChannel::class,
            'freight' => 'decimal:2',
            'insurance' => 'decimal:2',
            'local_costs' => 'decimal:2',
            'shipped_at' => 'date',
            'estimated_release_at' => 'date',
            'released_at' => 'date',
            'delivered_at' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ImportItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_date');
    }

    public function totalFob(): float
    {
        return round($this->items->sum(fn (ImportItem $item) => $item->fobTotal()), 2);
    }

    /** CIF = FOB + Flete + Seguro */
    public function calculateCif(): float
    {
        return round($this->totalFob() + (float) $this->freight + (float) $this->insurance, 2);
    }

    public function calculateTaxes(): float
    {
        return $this->liquidation()['totals']['taxes'];
    }

    public function calculateLandedCost(): float
    {
        return $this->liquidation()['totals']['total'];
    }

    public function isReleased(): bool
    {
        return $this->released_at !== null;
    }

    /** Desviación = (Días reales − Días estimados) ÷ Días estimados */
    public function calculateDeviation(): ?float
    {
        if (! $this->isReleased() || ! $this->shipped_at || ! $this->estimated_release_at) {
            return null;
        }

        $estimated = $this->shipped_at->diffInDays($this->estimated_release_at);
        $real = $this->shipped_at->diffInDays($this->released_at);

        return $estimated > 0 ? ($real - $estimated) / $estimated : null;
    }

    public function getCompliance(): ?Compliance
    {
        $deviation = $this->calculateDeviation();

        return $deviation === null ? null : Compliance::fromDeviation($deviation);
    }

    /**
     * Liquidación de la importación con las tasas vigentes que configura el Administrador.
     *
     * @return array{items: array<int, array>, totals: array<string, float>, rates: array<string, float>}
     */
    public function liquidation(): array
    {
        $this->loadMissing('items.tariffItem');

        $rates = [
            'iva' => TaxRate::rateFor(TaxRate::IVA),
            'fodinfa' => TaxRate::rateFor(TaxRate::FODINFA),
        ];

        // El flete, el seguro y los gastos locales son de toda la importación:
        // se reparten entre los productos según el FOB de cada uno.
        $fobs = $this->items->map(fn (ImportItem $item) => $item->fobTotal())->all();
        $freightAndInsurance = Money::distribute((float) $this->freight + (float) $this->insurance, $fobs);
        $localCosts = Money::distribute((float) $this->local_costs, $fobs);

        $rows = [];
        foreach ($this->items->values() as $i => $item) {
            $rows[] = $item->liquidate($freightAndInsurance[$i], $localCosts[$i], $rates['iva'], $rates['fodinfa']);
        }

        $sum = fn (string $key) => round(array_sum(array_column($rows, $key)), 2);

        $totals = [
            'fob' => $sum('fob'),
            'cif' => $sum('cif'),
            'ad_valorem' => $sum('ad_valorem'),
            'fodinfa' => $sum('fodinfa'),
            'iva' => $sum('iva'),
            'taxes' => $sum('taxes'),
            'local_costs' => $sum('local_costs'),
            'total' => $sum('total'),
        ];
        $totals['over_fob'] = $totals['fob'] > 0 ? ($totals['total'] - $totals['fob']) / $totals['fob'] : 0.0;

        return ['items' => $rows, 'totals' => $totals, 'rates' => $rates];
    }
}
