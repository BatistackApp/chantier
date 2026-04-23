<?php

namespace App\Filament\Resources\ProjectReports\Schemas;

use App\Enums\ProjectReportType;
use App\Models\ProjectReport;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class ProjectReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Projet')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('project_id')
                            ->relationship('project', 'reference')
                            ->searchable(['reference', 'title'])
                            ->preload()
                            ->required()
                            ->label('Chantier')
                            ->disabled(fn (?ProjectReport $record) => $record !== null)
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->reference} - {$record->title}"),
                    ]),

                Section::make('Type de constat')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('type')
                            ->label('Type de PV')
                            ->options(ProjectReportType::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->disabled(fn (?ProjectReport $record) => $record !== null),
                    ]),

                Section::make('Réception des supports')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('supports_conformity')
                            ->label('Attentes et platines conformes aux plans')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('access_ok')
                            ->label('Zone accessible (Camions / Levage)')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('electricity_ok')
                            ->label('Points électriques opérationnels')
                            ->default(false)
                            ->inline(false),

                        Textarea::make('support_deviations')
                            ->label('Écarts constatés')
                            ->rows(3)
                            ->placeholder('Décrire les anomalies relevées...')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->visible(fn (Get $get) => isset($get('type')->value) && $get('type')->value === ProjectReportType::Start->value),

                Section::make('État d\'achèvement')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_completed')
                            ->label('Travaux achevés conformément au contrat')
                            ->default(false)
                            ->inline(false),

                        Toggle::make('cleaning_done')
                            ->label('Nettoyage de la zone effectué')
                            ->default(false)
                            ->inline(false),

                        Repeater::make('reserves')
                            ->label('Réserves éventuelles')
                            ->schema([
                                Textarea::make('description')
                                    ->label('Description de la réserve')
                                    ->required()
                                    ->rows(2),
                            ])
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une réserve')
                            ->collapsible(),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get) => isset($get('type')->value) && $get('type')->value === ProjectReportType::Start->value),

                Section::make('Signature')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('signatory_name')
                                    ->label('Nom du signataire (Client)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Représentant entreprise cliente'),

                                DateTimePicker::make('signed_at')
                                    ->label('Date et heure de signature')
                                    ->required()
                                    ->native(false)
                                    ->default(now()),
                            ]),

                        SignaturePad::make('signature')
                            ->label(__('Signature du client'))
                            ->dotSize(2.0)
                            ->lineMinWidth(0.5)
                            ->lineMaxWidth(2.5)
                            ->throttle(16)
                            ->minDistance(5)
                            ->velocityFilterWeight(0.7),
                    ]),

            ]);
    }
}
