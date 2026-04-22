<?php

namespace App\Filament\Resources\Fabrications\Pages;

use App\Filament\Resources\Fabrications\FabricationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFabrication extends CreateRecord
{
    protected static string $resource = FabricationResource::class;
    protected static ?string $breadcrumb = 'Nouvelle fabrication';
    protected static ?string $title = 'Nouvelle fabrication';
}
