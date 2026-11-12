<?php

namespace App\Support;

/**
 * Operaciones con dinero en centavos (enteros) para evitar errores de redondeo
 * de punto flotante: 15% de 2.408,90 debe dar 361,34 y no 361,33.
 */
class Money
{
    public static function toCents(float|int|string|null $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public static function fromCents(int|float $cents): float
    {
        return round($cents / 100, 2);
    }

    /** Porcentaje de un monto, redondeado al centavo. Ej.: percent(2408.90, 15) = 361.34 */
    public static function percent(float $amount, float $rate): float
    {
        $cents = self::toCents($amount) * (int) round($rate * 100);   // centavos × puntos básicos

        return self::fromCents(round($cents / 10000));
    }

    /**
     * Reparte $amount entre varias partes según su peso (ej. el flete según el FOB de cada producto).
     *
     * Cada parte se redondea al centavo y la última recibe lo que falta para completar el monto.
     * Así la suma de las partes siempre es exactamente $amount: 100 entre 3 partes iguales
     * da 33,33 + 33,33 + 33,34 y no se pierde ningún centavo.
     *
     * @param  array<int, float>  $weights
     * @return array<int, float>
     */
    public static function distribute(float $amount, array $weights): array
    {
        $cents = self::toCents($amount);
        $totalWeight = array_sum($weights);
        $last = count($weights) - 1;

        $shares = [];
        $assigned = 0;   // centavos ya repartidos

        foreach (array_values($weights) as $i => $weight) {
            if ($i === $last) {
                $share = $cents - $assigned;
            } else {
                $share = $totalWeight > 0 ? (int) round($cents * $weight / $totalWeight) : 0;
            }

            $assigned += $share;
            $shares[] = self::fromCents($share);
        }

        return $shares;
    }

    public static function format(float|int|string|null $amount): string
    {
        return '$'.number_format((float) $amount, 2, ',', '.');
    }

    /** Porcentaje para mostrar, sin ceros de más: 15.00 → "15%", 0.50 → "0,5%". */
    public static function formatPercent(float|int|string|null $rate): string
    {
        $number = number_format((float) $rate, 2, ',', '');

        return rtrim(rtrim($number, '0'), ',').'%';
    }
}
