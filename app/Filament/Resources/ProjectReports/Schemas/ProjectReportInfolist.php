<?php

namespace App\Filament\Resources\ProjectReports\Schemas;

use App\Enums\ProjectReportType;
use App\Models\ProjectReport;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(fn (ProjectReport $record) => $record->type === ProjectReportType::End ? 3 : 2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informations')
                            ->schema([
                                TextEntry::make('project.reference')
                                    ->label('Chantier'),

                                TextEntry::make('type')
                                    ->badge(),

                                TextEntry::make('signatory_name')
                                    ->label('Signataire'),

                                TextEntry::make('signed_at')
                                    ->label('Date Signature')
                                    ->date('d/m/Y à H:i'),
                            ]),

                        Section::make('Constats (Démarrage)')
                            ->visible(fn(ProjectReport $record) => $record->type === ProjectReportType::Start || $record->type === ProjectReportType::End)
                            ->columns(3)
                            ->schema([
                                IconEntry::make('supports_conformity')
                                    ->label('Supports conformes')
                                    ->boolean(),

                                IconEntry::make('access_ok')
                                    ->label('Accès OK')
                                    ->boolean(),

                                IconEntry::make('electricity_ok')
                                    ->label('Électricité OK')
                                    ->boolean(),

                                TextEntry::make('support_deviations')
                                    ->label('Anomalies')
                                    ->placeholder('Aucune')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('État Final (Fin)')
                            ->visible(fn (ProjectReport $record) => $record->type === ProjectReportType::End)
                            ->columns(2)
                            ->schema([
                                IconEntry::make('is_completed')
                                    ->label('Travaux achevés')
                                    ->boolean(),

                                IconEntry::make('cleaning_done')
                                    ->label('Nettoyage effectué')
                                    ->boolean(),

                                RepeatableEntry::make('reserves')
                                    ->label('Réserves')
                                    ->schema([
                                        TextEntry::make('description'),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
