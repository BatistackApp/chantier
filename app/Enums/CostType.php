<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum CostType: string implements HasLabel
{
    case LABOR = 'labor';          // Main d'œuvre
    case MATERIAL = 'material';    // Fournitures / Matériaux
    case MANUFACTURING = 'manufacturing'; // Fabrication Atelier
    case RENTAL = 'rental';        // Location engins
    case SUBCONTRACTING = 'subcontracting'; // Sous-traitance


    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::LABOR => 'Main d\'œuvre',
            self::MATERIAL => 'Fournitures / Matériaux',
            self::MANUFACTURING => 'Fabrication Atelier',
            self::RENTAL => 'Location engins',
            self::SUBCONTRACTING => 'Sous-traitance',
            default => null,
        };
    }
}
