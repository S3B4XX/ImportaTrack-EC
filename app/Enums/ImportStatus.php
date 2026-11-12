<?php

namespace App\Enums;

enum ImportStatus: string
{
    case Registrada = 'REGISTRADA';
    case Embarcada = 'EMBARCADA';
    case EnTransito = 'EN_TRANSITO';
    case Arribo = 'ARRIBO';
    case EnAforo = 'EN_AFORO';
    case Liberada = 'LIBERADA';
    case Entregada = 'ENTREGADA';

    public function label(): string
    {
        return match ($this) {
            self::Registrada => 'Registrada',
            self::Embarcada => 'Embarcada',
            self::EnTransito => 'En tránsito',
            self::Arribo => 'Arribo',
            self::EnAforo => 'En aforo',
            self::Liberada => 'Liberada',
            self::Entregada => 'Entregada',
        };
    }

    /** Clases de Tailwind para la etiqueta de estado. */
    public function badge(): string
    {
        return match ($this) {
            self::Registrada => 'bg-slate-100 text-slate-700',
            self::Embarcada, self::EnTransito => 'bg-blue-50 text-blue-700',
            self::Arribo, self::EnAforo => 'bg-amber-50 text-amber-700',
            self::Liberada, self::Entregada => 'bg-green-50 text-green-700',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
