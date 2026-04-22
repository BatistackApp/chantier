<?php

namespace App\Filament\Resources\ProjectReports\Schemas;

use App\Enums\ProjectReportType;
use App\Models\ProjectReport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

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
                    ->schema([])
                    ->visible(fn (Get $get) => isset($get('type')->value) && $get('type')->value === ProjectReportType::Start->value),

            ]);
    }
}
