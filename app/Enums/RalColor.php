<?php

namespace App\Enums;

enum RalColor: string
{
    case RAL_1015 = '1015';
    case RAL_3000 = '3000';
    case RAL_5002 = '5002';
    case RAL_6005 = '6005';
    case RAL_7016 = '7016';
    case RAL_7035 = '7035';
    case RAL_9005 = '9005';
    case RAL_9010 = '9010';

    /**
     * Retourne la valeur Hexadécimale associée au code RAL.
     */
    public function hex(): string
    {
        return match ($this) {
            self::RAL_1015 => '#E6D690',
            self::RAL_3000 => '#AF2B1E',
            self::RAL_5002 => '#202060',
            self::RAL_6005 => '#2F4538',
            self::RAL_7016 => '#383E42',
            self::RAL_7035 => '#D7D7D7',
            self::RAL_9005 => '#0A0A0A',
            self::RAL_9010 => '#F1F1E1',
        };
    }

    /**
     * Retourne le nom complet en français.
     */
    public function label(): string
    {
        return match ($this) {
            self::RAL_1015 => 'Ivoire clair',
            self::RAL_3000 => 'Rouge feu',
            self::RAL_5002 => 'Bleu outremer',
            self::RAL_6005 => 'Vert mousse',
            self::RAL_7016 => 'Gris anthracite',
            self::RAL_7035 => 'Gris clair',
            self::RAL_9005 => 'Noir foncé',
            self::RAL_9010 => 'Blanc pur',
        };
    }

    /**
     * Helper pour obtenir tous les codes Hex pour le ColorPicker Filament.
     */
    public static function allHex(): array
    {
        return collect(self::cases())->map(fn (self $color) => $color->hex())->toArray();
    }

    /**
     * Helper pour le composant Select de Filament.
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $color) => [
            $color->value => "RAL {$color->value} - {$color->label()}"
        ])->toArray();
    }
}
