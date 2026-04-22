<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
    protected static ?string $breadcrumb = 'Nouveau Chantier';
    protected static ?string $title = 'Nouveau Chantier';
}
