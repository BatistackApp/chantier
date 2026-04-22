<?php

namespace App\Filament\Resources\ProjectCosts\Pages;

use App\Filament\Resources\ProjectCosts\ProjectCostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectCost extends CreateRecord
{
    protected static string $resource = ProjectCostResource::class;
    protected static ?string $title = 'Nouveau Coûts';
    protected static ?string $breadcrumb = 'Nouveau Coûts';
}
