<?php

namespace Tests\Unit;

use App\Rules\EcuadorianId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EcuadorianIdTest extends TestCase
{
    public static function validIds(): array
    {
        return [
            'cédula' => ['1710034065'],
            'cédula Guayas' => ['0918765439'],
            'RUC persona natural' => ['1712345675001'],
            'RUC sociedad privada' => ['1792345677001'],
            'RUC sociedad privada Guayas' => ['0991876545001'],
            'RUC sector público (SRI)' => ['1760001550001'],
        ];
    }

    public static function invalidIds(): array
    {
        return [
            'dígito verificador incorrecto' => ['1710034066', 'dígito verificador'],
            'RUC privado con verificador incorrecto' => ['1792345678001', 'dígito verificador'],
            'provincia inexistente' => ['2512345675', 'provincia'],
            'tercer dígito 7' => ['1772345677001', 'tercer dígito'],
            'establecimiento 000' => ['1712345675000', 'establecimiento'],
            'con letras' => ['17A2345675', 'solo puede contener números'],
            'longitud incorrecta' => ['171234567', '10 dígitos'],
        ];
    }

    #[DataProvider('validIds')]
    public function test_acepta_identificaciones_validas(string $id): void
    {
        $this->assertNull($this->validate($id));
    }

    #[DataProvider('invalidIds')]
    public function test_rechaza_identificaciones_invalidas(string $id, string $reason): void
    {
        $error = $this->validate($id);

        $this->assertNotNull($error, "Se esperaba que {$id} fuera rechazado");
        $this->assertStringContainsString($reason, $error);
    }

    public function test_el_modo_ruc_rechaza_cedulas(): void
    {
        $this->assertStringContainsString('13 dígitos', $this->validate('1710034065', EcuadorianId::RUC));
    }

    private function validate(string $value, string $type = EcuadorianId::ANY): ?string
    {
        $error = null;
        (new EcuadorianId($type))->validate('ruc', $value, function (string $message) use (&$error) {
            $error = $message;
        });

        return $error;
    }
}
