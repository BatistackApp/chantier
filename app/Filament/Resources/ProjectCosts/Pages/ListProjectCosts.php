<?php

namespace App\Filament\Resources\ProjectCosts\Pages;

use App\Filament\Resources\ProjectCosts\ProjectCostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListProjectCosts extends ListRecords
{
    protected static string $resource = ProjectCostResource::class;
    protected static ?string $title = 'Liste des Coûts';
    protected static ?string $breadcrumb = 'Liste des Coûts';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau coût')
                ->icon(Heroicon::PlusCircle),
        ];
    }
}
