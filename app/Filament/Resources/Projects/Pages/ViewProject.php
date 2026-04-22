<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;
    protected static ?string $breadcrumb = 'Chantier';
    protected static ?string $title = 'Chantier';


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
