<?php

namespace App\Filament\Resources\Fabrications\Pages;

use App\Filament\Resources\Fabrications\FabricationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFabrication extends EditRecord
{
    protected static string $resource = FabricationResource::class;
    protected static ?string $breadcrumb = 'Éditer une fabrication';
    protected static ?string $title = 'Éditer une fabrication';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
