<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum FabricationType: string implements HasLabel
{
    case FOLDING = 'folding';     // Pliage
    case HARDWARE = 'hardware';   // Visserie / Quincaillerie
    case ASSEMBLY = 'assembly';   // Fabrication pure (Portail, etc)

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::FOLDING => 'Pliage',
            self::HARDWARE => 'Visserie / Quincaillerie',
            self::ASSEMBLY => 'Fabrication pure (Portail, etc)',
            default => null,
        };
    }
}
