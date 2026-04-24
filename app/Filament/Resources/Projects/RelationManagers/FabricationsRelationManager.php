<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\FabricationType;
use App\Filament\Resources\Projects\ProjectResource;
use App\Services\DocumentService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class FabricationsRelationManager extends RelationManager
{
    protected static string $relationship = 'fabrications';

    protected static ?string $title = 'Fabrications Atelier';

    protected static ?string $relatedResource = ProjectResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('label')
                    ->searchable(),

                TextColumn::make('dimensions'),

                TextColumn::make('quantity')
                    ->alignEnd(),

                TextColumn::make('color_code')
                    ->badge()
                    ->state(fn ($record) => "RAL {$record->color_code}")
                    ->extraAttributes(fn (string $state, $record) => [
                        'style' => "background-color: var(--color-ral-{$record->color_code}); color: white;",
                    ])
                    ->label('Couleur'),

                TextColumn::make('unit_cost')
                    ->money('EUR')
                    ->alignEnd(),

                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn ($record) => $record->quantity * $record->unit_cost)
                    ->money('EUR')
                    ->weight('bold')
                    ->alignEnd(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(FabricationType::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nouvelle Fabrication')
                    ->icon(Phosphor::PlusCircle),

                Action::make('generate_sheet')
                    ->label('Générer Fiche Fabrication')
                    ->icon(Heroicon::DocumentDuplicate)
                    ->action(function () {
                        $service = app(DocumentService::class);
                        $path = $service->generateFabricationSheet($this->ownerRecord);

                        return response()->download($path);
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(FabricationType::class)
                    ->required()
                    ->native(false),

                TextInput::make('label')
                    ->required()
                    ->placeholder('Ex: Pliage R1 DEV515')
                    ->maxLength(255),

                TextInput::make('dimensions')
                    ->placeholder('Ex: 3ml')
                    ->maxLength(100),

                TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->minValue(0)
                    ->required(),

                TextInput::make('color_code')
                    ->placeholder('Ex: RAL 7016'),

                TextInput::make('unit_cost')
                    ->numeric()
                    ->prefix('€')
                    ->helperText('Cout unitaire de fabrication'),
            ]);
    }
}
