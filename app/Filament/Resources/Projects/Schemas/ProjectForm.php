<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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

                Section::make('Planification et Budget')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::DRAFT)
                            ->required()
                            ->native(false),

                        DatePicker::make('planned_start_date')
                            ->date('d/m/Y')
                            ->label('Date prévisionnelle démarrage'),

                        DatePicker::make('planned_end_date')
                            ->date('d/m/Y')
                            ->label('Date prévisionnelle de fin'),

                        TextInput::make('quoted_amount')
                            ->label('Montant Vendu (Devis/Commande) HT')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Montant contractuel du chantier')
                            ->required(),

                        TextInput::make('estimated_cost')
                            ->label('Coût Prévisionel (Etude) HT')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Budget estimé lors de l\'étude'),
                    ]),

                Section::make('Dates Réelles')
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('started_at')
                            ->label('Démarrage effectif')
                            ->native(false)
                            ->helperText('Renseigné automatiquement via PV de démarrage')
                            ->disabled(),

                        DateTimePicker::make('ended_at')
                            ->label('Fin effective')
                            ->native(false)
                            ->helperText('Renseigné automatiquement via PV de fin')
                            ->disabled(),
                    ]),
            ]);
    }
}
