<?php

namespace App\Filament\Resources\ProjectCosts\Schemas;

use App\Enums\CostType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectCostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Projet Associé')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('project_id')
                            ->relationship('project', 'reference')
                            ->searchable(['reference', 'title'])
                            ->preload()
                            ->required()
                            ->label('Chantier')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->reference} - {$record->title}"),
                    ]),

                Section::make('Détails du coûts')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('label')
                            ->label('Libellé')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Main d\'œuvre semaine 12')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                Select::make('cost_type')
                                    ->label('Type de coût')
                                    ->options(CostType::class)
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                TextInput::make('amount')
                                    ->label('Montant (€ HT)')
                                    ->numeric()
                                    ->prefix('€')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Montant hors taxes'),

                                DatePicker::make('spent_at')
                                    ->label('Date de dépense')
                                    ->required()
                                    ->date('d/m/Y')
                                    ->default(now())
                                    ->maxDate(now()),
                            ])
                    ]),
            ]);
    }
}
