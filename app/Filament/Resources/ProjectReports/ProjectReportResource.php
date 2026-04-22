<?php

namespace App\Filament\Resources\ProjectReports;

use App\Filament\Resources\ProjectReports\Pages\CreateProjectReport;
use App\Filament\Resources\ProjectReports\Pages\EditProjectReport;
use App\Filament\Resources\ProjectReports\Pages\ListProjectReports;
use App\Filament\Resources\ProjectReports\Pages\ViewProjectReport;
use App\Filament\Resources\ProjectReports\Schemas\ProjectReportForm;
use App\Filament\Resources\ProjectReports\Schemas\ProjectReportInfolist;
use App\Filament\Resources\ProjectReports\Tables\ProjectReportsTable;
use App\Models\ProjectReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjectReportResource extends Resource
{
    protected static ?string $model = ProjectReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentCheck;

    protected static ?string $navigationLabel = 'Procès Verbaux';

    protected static ?string $modelLabel = 'PV';

    protected static ?int $navigationSort = 5;

    protected static string|UnitEnum|null $navigationGroup = 'Production';

    public static function form(Schema $schema): Schema
    {
        return ProjectReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectReports::route('/'),
            'create' => CreateProjectReport::route('/create'),
            'view' => ViewProjectReport::route('/{record}'),
            'edit' => EditProjectReport::route('/{record}/edit'),
        ];
    }
}
