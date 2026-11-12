<?php

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_porcentaje_redondea_al_centavo_sin_errores_de_punto_flotante(): void
    {
        // 15% × 2.408,90 = 361,335 → 361,34 (con floats daría 361,33)
        $this->assertSame(361.34, Money::percent(2408.90, 15));
        $this->assertSame(43.60, Money::percent(8720, 0.5));
    }

    public function test_reparte_un_monto_segun_el_peso_del_fob(): void
    {
        $this->assertSame([720.00, 180.00], Money::distribute(900, [8000, 2000]));
    }

    public function test_el_reparto_no_pierde_centavos(): void
    {
        // 100 entre 3 partes iguales: la última recibe el centavo que sobra.
        $shares = Money::distribute(100, [100, 100, 100]);

        $this->assertSame([33.33, 33.33, 33.34], $shares);
        $this->assertSame(100.00, round(array_sum($shares), 2));
    }

    public function test_formatea_porcentajes_sin_ceros_de_mas(): void
    {
        $this->assertSame('15%', Money::formatPercent('15.00'));
        $this->assertSame('0,5%', Money::formatPercent(0.5));
        $this->assertSame('0%', Money::formatPercent(0));
    }
}
