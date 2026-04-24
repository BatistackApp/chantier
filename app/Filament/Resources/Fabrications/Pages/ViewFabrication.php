<?php

namespace App\Filament\Resources\Fabrications\Pages;

use App\Filament\Resources\Fabrications\FabricationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewFabrication extends ViewRecord
{
    protected static string $resource = FabricationResource::class;
    protected static ?string $breadcrumb = 'Fiche Fabrication';
    protected static ?string $title = 'Fiche Fabrication';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::PencilSquare)
                ->label('Modifier Fabrication'),
        ];
    }
}
