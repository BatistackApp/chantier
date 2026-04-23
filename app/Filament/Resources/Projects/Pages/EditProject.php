<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;
    protected static ?string $breadcrumb = 'Modifier le chantier';
    protected static ?string $title = 'Modifier le chantier';


    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::Eye)
                ->label('Fiche chantier'),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->label('Supprimer chantier'),
        ];
    }
}
