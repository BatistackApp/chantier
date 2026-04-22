<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use ToneGabes\Filament\Icons\Enums\Phosphor;

enum ProjectStatus: string implements HasColor, HasDescription, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case PREPARATION = 'preparation';
    case STARTED = 'started';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PREPARATION => 'warning',
            self::STARTED, self::FINISHED => 'success',
            self::CANCELLED => 'danger',
            default => null,
        };
    }

    public function getDescription(): string|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'En cours de préparation',
            self::PREPARATION => 'En préparation',
            self::STARTED => 'Chantier Démarrer',
            self::FINISHED => 'Chantier Terminer',
            self::CANCELLED => 'Chantier Annulé',
            default => null,
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => Phosphor::PencilCircle,
            self::PREPARATION => Phosphor::Gear,
            self::STARTED => Phosphor::PlayCircle,
            self::FINISHED => Phosphor::CheckCircle,
            self::CANCELLED => Phosphor::XCircle,
            default => null,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::PREPARATION => 'Préparation',
            self::STARTED => 'Démarrer',
            self::FINISHED => 'Terminer',
            self::CANCELLED => 'Annuler',
            default => null,
        };
    }
}
