<?php

namespace App\Filament\Resources\Fabrications\Pages;

use App\Filament\Resources\Fabrications\FabricationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListFabrications extends ListRecords
{
    protected static string $resource = FabricationResource::class;
    protected static ?string $breadcrumb = 'Fabrication';
    protected static ?string $title = 'Fabrication';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvelle fabrication')
                ->icon(Heroicon::PlusCircle),
        ];
    }
}
