<?php

namespace App\Enums;

enum TransportMode: string
{
    case Maritimo = 'MARITIMO';
    case Aereo = 'AEREO';
    case Terrestre = 'TERRESTRE';

    public function label(): string
    {
        return match ($this) {
            self::Maritimo => 'Marítimo',
            self::Aereo => 'Aéreo',
            self::Terrestre => 'Terrestre',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
