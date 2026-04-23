<?php

namespace App\Filament\Resources\ProjectReports\Pages;

use App\Filament\Resources\ProjectReports\ProjectReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectReport extends CreateRecord
{
    protected static string $resource = ProjectReportResource::class;
    protected static ?string $title = 'Nouveau PV';
    protected static ?string $breadcrumb = 'Nouveau PV';
}
