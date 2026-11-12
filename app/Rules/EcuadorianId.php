<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida en el servidor una cédula (10 dígitos) o un RUC (13 dígitos) de Ecuador.
 *
 * - Cédula y RUC de persona natural (3er dígito 0-5): módulo 10 con coeficientes 2,1,2,1…
 * - RUC de sociedad pública (3er dígito 6): módulo 11 con coeficientes 3,2,7,6,5,4,3,2.
 * - RUC de sociedad privada o extranjero (3er dígito 9): módulo 11 con coeficientes 4,3,2,7,6,5,4,3,2.
 * - Los 2 primeros dígitos son la provincia (01-24, o 30 para ecuatorianos en el exterior).
 *
 * Esta regla es la que protege la base de datos: aunque alguien desactive el
 * JavaScript o envíe la petición con Postman, un RUC inválido nunca se guarda.
 */
class EcuadorianId implements ValidationRule
{
    public const ANY = 'any';
    public const RUC = 'ruc';
    public const CEDULA = 'cedula';

    public function __construct(private string $type = self::ANY) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // is_scalar descarta arreglos (ruc[]=...): convertirlos a texto daría un error 500.
        if (! is_scalar($value) || ! ctype_digit((string) $value)) {
            $fail('El :attribute solo puede contener números.');

            return;
        }

        $value = (string) $value;

        $length = strlen($value);

        if ($this->type === self::RUC && $length !== 13) {
            $fail('El :attribute debe ser un RUC de 13 dígitos.');

            return;
        }

        if ($this->type === self::CEDULA && $length !== 10) {
            $fail('El :attribute debe ser una cédula de 10 dígitos.');

            return;
        }

        if (! in_array($length, [10, 13], true)) {
            $fail('El :attribute debe tener 10 dígitos (cédula) o 13 dígitos (RUC).');

            return;
        }

        $error = $length === 10 ? self::cedulaError($value) : self::rucError($value);

        if ($error !== null) {
            $fail("El :attribute no es válido: {$error}.");
        }
    }

    public static function isValidCedula(string $value): bool
    {
        return ctype_digit($value) && strlen($value) === 10 && self::cedulaError($value) === null;
    }

    public static function isValidRuc(string $value): bool
    {
        return ctype_digit($value) && strlen($value) === 13 && self::rucError($value) === null;
    }

    /** Devuelve el motivo del error o null si la cédula es válida. */
    private static function cedulaError(string $id): ?string
    {
        if ($error = self::provinceError($id)) {
            return $error;
        }

        if ((int) $id[2] >= 6) {
            return 'el tercer dígito de una cédula debe ser menor a 6';
        }

        return self::modulo10($id) ? null : 'el dígito verificador no coincide';
    }

    /** Devuelve el motivo del error o null si el RUC es válido. */
    private static function rucError(string $ruc): ?string
    {
        if ($error = self::provinceError($ruc)) {
            return $error;
        }

        $third = (int) $ruc[2];

        // Persona natural: cédula + establecimiento (001, 002…)
        if ($third < 6) {
            if (! self::modulo10(substr($ruc, 0, 10))) {
                return 'el dígito verificador no coincide';
            }

            return (int) substr($ruc, 10, 3) > 0 ? null : 'el código de establecimiento no puede ser 000';
        }

        // Sociedad pública: verificador en la posición 9 y establecimiento de 4 dígitos.
        if ($third === 6) {
            if (! self::modulo11(substr($ruc, 0, 8), [3, 2, 7, 6, 5, 4, 3, 2], (int) $ruc[8])) {
                return 'el dígito verificador no coincide';
            }

            return (int) substr($ruc, 9, 4) > 0 ? null : 'el código de establecimiento no puede ser 0000';
        }

        // Sociedad privada o extranjero sin cédula.
        if ($third === 9) {
            if (! self::modulo11(substr($ruc, 0, 9), [4, 3, 2, 7, 6, 5, 4, 3, 2], (int) $ruc[9])) {
                return 'el dígito verificador no coincide';
            }

            return (int) substr($ruc, 10, 3) > 0 ? null : 'el código de establecimiento no puede ser 000';
        }

        return 'el tercer dígito debe ser 0-5 (persona natural), 6 (sector público) o 9 (sociedad privada)';
    }

    private static function provinceError(string $id): ?string
    {
        $province = (int) substr($id, 0, 2);

        return ($province >= 1 && $province <= 24) || $province === 30
            ? null
            : 'los dos primeros dígitos deben ser un código de provincia (01-24 o 30)';
    }

    /** Módulo 10 sobre los 10 dígitos de una cédula. */
    private static function modulo10(string $cedula): bool
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $product = (int) $cedula[$i] * ($i % 2 === 0 ? 2 : 1);
            $sum += $product > 9 ? $product - 9 : $product;
        }

        $check = (10 - ($sum % 10)) % 10;

        return $check === (int) $cedula[9];
    }

    /** Módulo 11 con los coeficientes dados. */
    private static function modulo11(string $digits, array $coefficients, int $expected): bool
    {
        $sum = 0;
        foreach ($coefficients as $i => $coefficient) {
            $sum += (int) $digits[$i] * $coefficient;
        }

        $check = 11 - ($sum % 11);
        if ($check === 11) {
            $check = 0;
        }

        return $check !== 10 && $check === $expected;
    }
}
