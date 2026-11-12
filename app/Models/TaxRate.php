<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/** Tasas de tributos que usa la liquidación (IVA y FODINFA). */
class TaxRate extends Model
{
    public const IVA = 'IVA';
    public const FODINFA = 'FODINFA';

    protected $fillable = ['code', 'name', 'rate', 'valid_from'];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'valid_from' => 'date',
        ];
    }

    /** Porcentaje vigente para un tributo (ej. 15.00 para IVA). */
    public static function rateFor(string $code): float
    {
        $rate = static::where('code', $code)->value('rate');

        // Sin la tasa no se puede liquidar: es mejor detenerse que calcular con 0% sin avisar.
        if ($rate === null) {
            throw new RuntimeException("Falta configurar la tasa {$code} en la tabla tax_rates.");
        }

        return (float) $rate;
    }
}
