<?php

namespace App\Filament\Resources\Fabrications\Schemas;

use App\Enums\FabricationType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class FabricationForm
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
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->reference} - {$record->title}"),
                    ]),

                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('type')
                            ->label('Type de Fabrication')
                            ->options(FabricationType::class)
                            ->required()
                            ->native(false)
                            ->live(),

                        TextInput::make('label')
                            ->label('Désignation')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Pliage R1 DEV515'),

                        TextInput::make('dimensions')
                            ->label('Dimensions')
                            ->maxLength(255)
                            ->placeholder('Ex: 3ml'),

                        TextInput::make('quantity')
                            ->label('Quantité')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(0.01)
                            ->step(0.01)
                            ->live(onBlur: true),

                        TextInput::make('color_code')
                            ->label('Code couleur')
                            ->maxLength(50)
                            ->placeholder('Ex: RAL 7016')
                            ->helperText('Norme RAL ou autre référence'),

                        TextInput::make('unit_cost')
                            ->label('Coût unitaire (€)')
                            ->numeric()
                            ->prefix('€')
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true),

                        TextEntry::make('total_cost')
                            ->label('Coût total')
                            ->state(function (Get $get): string {
                                $quantity = floatval($get('quantity') ?? 0);
                                $unitCost = floatval($get('unit_cost') ?? 0);
                                $total = $quantity * $unitCost;

                                return number_format($total, 2, ',', ' ').' €';
                            })
                            ->extraAttributes(['class' => 'text-lg font-bold text-success-600']),

                        TimePicker::make('time_realized')
                            ->label('Temps de réalisation')
                            ->seconds(false)
                            ->native(false)
                            ->helperText('Temps effectif passé en atelier'),
                    ])->columns(4),
            ]);
    }
}
