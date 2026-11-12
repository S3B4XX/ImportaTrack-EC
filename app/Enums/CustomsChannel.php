<?php

namespace App\Enums;

enum CustomsChannel: string
{
    case Automatico = 'AUTOMATICO';
    case Documental = 'DOCUMENTAL';
    case Fisico = 'FISICO';

    public function label(): string
    {
        return match ($this) {
            self::Automatico => 'Automático',
            self::Documental => 'Documental',
            self::Fisico => 'Físico',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
