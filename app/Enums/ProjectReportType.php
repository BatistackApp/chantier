<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ProjectReportType: string implements HasLabel
{
    case Start = 'start';
    case End = 'end';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Start => 'Début',
            self::End => 'Fin',
            default => null,
        };
    }
}
