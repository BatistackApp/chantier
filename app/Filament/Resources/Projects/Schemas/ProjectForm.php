<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Client')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nom/Raison Social')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),

                                TextInput::make('siret')
                                    ->maxLength(14),

                                Toggle::make('is_professional')
                                    ->default(true)
                                    ->label('Client Professionnel'),
                            ])
                            ->label('Client'),
                    ]),

                Section::make('Détails du Projet')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('reference')
                                    ->label('Réference du Projet')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('CHA-2026-001')
                                    ->helperText('Référence unique du chantier')
                                    ->required(),

                                TextInput::make('title')
                                    ->label('Désignation du chantier')
                                    ->placeholder('TENERGIE SALA')
                                    ->columnSpanFull()
                                    ->maxLength(255)
                                    ->required(),

                                Textarea::make('address')
                                    ->label('Adresse du chantier')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->required(),


                            ]),
                    ]),
            ]);
    }
}
