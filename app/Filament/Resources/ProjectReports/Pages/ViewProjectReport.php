<?php

namespace App\Filament\Resources\ProjectReports\Pages;

use App\Filament\Resources\ProjectReports\ProjectReportResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewProjectReport extends ViewRecord
{
    protected static string $resource = ProjectReportResource::class;
    protected static ?string $title = 'Procès Verbal';
    protected static ?string $breadcrumb = 'Procès Verbal';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::PencilSquare)
                ->label('Éditer PV'),
        ];
    }
}
