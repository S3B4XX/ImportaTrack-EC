<?php

namespace App\Enums;

/** Semáforo de cumplimiento de la especificación (sección 2.2). */
enum Compliance: string
{
    case ATiempo = 'A_TIEMPO';
    case RetrasoLeve = 'RETRASO_LEVE';
    case RetrasoCritico = 'RETRASO_CRITICO';

    /** Hasta 20% de desviación el retraso es leve; por encima es crítico. */
    public const MAX_RETRASO_LEVE = 0.20;

    public static function fromDeviation(float $deviation): self
    {
        return match (true) {
            $deviation <= 0 => self::ATiempo,
            $deviation <= self::MAX_RETRASO_LEVE => self::RetrasoLeve,
            default => self::RetrasoCritico,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ATiempo => 'A tiempo',
            self::RetrasoLeve => 'Retraso leve',
            self::RetrasoCritico => 'Retraso crítico',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ATiempo => '✓',
            self::RetrasoLeve => '!',
            self::RetrasoCritico => '✕',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ATiempo => 'bg-green-50 text-green-700',
            self::RetrasoLeve => 'bg-amber-50 text-amber-700',
            self::RetrasoCritico => 'bg-red-50 text-red-700',
        };
    }
}
