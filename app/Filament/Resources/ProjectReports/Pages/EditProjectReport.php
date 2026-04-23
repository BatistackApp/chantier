<?php

namespace App\Filament\Resources\ProjectReports\Pages;

use App\Filament\Resources\ProjectReports\ProjectReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditProjectReport extends EditRecord
{
    protected static string $resource = ProjectReportResource::class;
    protected static ?string $title = 'Editer Procès Verbal';
    protected static ?string $breadcrumb = 'Editer Procès Verbal';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::Eye)
                ->label('Fiche'),
            DeleteAction::make()
                ->label('Supprimer')
                ->icon(Heroicon::Trash),
        ];
    }
}
