<?php

namespace App\Filament\Resources\ProjectCosts;

use App\Filament\Resources\ProjectCosts\Pages\CreateProjectCost;
use App\Filament\Resources\ProjectCosts\Pages\EditProjectCost;
use App\Filament\Resources\ProjectCosts\Pages\ListProjectCosts;
use App\Filament\Resources\ProjectCosts\Schemas\ProjectCostForm;
use App\Filament\Resources\ProjectCosts\Tables\ProjectCostsTable;
use App\Models\ProjectCost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectCostResource extends Resource
{
    protected static ?string $model = ProjectCost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;
    protected static ?string $navigationLabel = 'Coûts Chantier';
    protected static ?string $modelLabel = 'Coûts';
    protected static string|null|\UnitEnum $navigationGroup = 'Production';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return ProjectCostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectCostsTable::configure($table);
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
            'index' => ListProjectCosts::route('/'),
            'create' => CreateProjectCost::route('/create'),
            'edit' => EditProjectCost::route('/{record}/edit'),
        ];
    }
}
