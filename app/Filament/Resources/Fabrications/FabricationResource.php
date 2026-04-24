<?php

namespace App\Filament\Resources\Fabrications;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Fabrications\Pages\CreateFabrication;
use App\Filament\Resources\Fabrications\Pages\EditFabrication;
use App\Filament\Resources\Fabrications\Pages\ListFabrications;
use App\Filament\Resources\Fabrications\Pages\ViewFabrication;
use App\Filament\Resources\Fabrications\Schemas\FabricationForm;
use App\Filament\Resources\Fabrications\Schemas\FabricationInfolist;
use App\Filament\Resources\Fabrications\Tables\FabricationsTable;
use App\Models\Fabrication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FabricationResource extends Resource
{
    protected static ?string $model = Fabrication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static ?string $navigationLabel = 'Fabrications';

    protected static ?string $modelLabel = 'Fabrication';

    protected static ?int $navigationSort = 3;

    protected static string|UnitEnum|null $navigationGroup = 'Production';
    protected static ?string $breadcrumb = 'Fabrications';

    public static function form(Schema $schema): Schema
    {
        return FabricationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FabricationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FabricationInfolist::configure($schema);
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
            'index' => ListFabrications::route('/'),
            'create' => CreateFabrication::route('/create'),
            'edit' => EditFabrication::route('/{record}/edit'),
            'view' => ViewFabrication::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('project', function ($query) {
            $query->where('status', ProjectStatus::PREPARATION);
        })->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
