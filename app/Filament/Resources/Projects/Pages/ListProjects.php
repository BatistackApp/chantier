<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;
    protected static ?string $breadcrumb = 'Liste des Chantiers';
    protected static ?string $title = 'Liste des Chantiers';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter un chantier')
                ->icon(Phosphor::PlusCircle),
        ];
    }
}
