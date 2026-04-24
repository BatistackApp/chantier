<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum FabricationItemType: string implements HasLabel
{
    case MATERIAL = 'material';
    case LABOR = 'labor';
    case OTHER = 'other';


    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::MATERIAL => 'Fournitures / Matériaux',
            self::LABOR => 'Main d\'œuvre',
            self::OTHER => 'Autre',
            default => null,
        };
    }
}
