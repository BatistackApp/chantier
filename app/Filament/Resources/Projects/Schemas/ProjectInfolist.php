<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Services\DocumentService;
use App\Services\FinancialService;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use SalemAljebaly\FilamentMapPicker\MapView;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Localisation')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Adresse du chantier')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),

                        MapView::make('location')
                            ->label('Localisation du chantier')
                            ->latlngFields('geo_lat', 'geo_long')
                            ->height(250),
                    ]),

                Grid::make(1)
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Group::make([
                                            TextEntry::make('reference')
                                                ->label('Référence')
                                                ->size('lg')
                                                ->weight('bold')
                                                ->copyable()
                                                ->copyMessage('Référence copiée')
                                                ->icon('heroicon-o-hashtag'),

                                            TextEntry::make('title')
                                                ->label('Désignation du chantier')
                                                ->size('lg')
                                                ->columnSpanFull(),

                                            TextEntry::make('customer.name')
                                                ->label('Client')
                                                ->icon('heroicon-o-building-office')
                                                ->url(fn($record) => route('filament.admin.resources.customers.view', ['record' => $record->customer_id]))
                                                ->color('primary'),
                                        ]),

                                        Group::make([
                                            TextEntry::make('status')
                                                ->badge()
                                                ->size('lg')
                                                ->formatStateUsing(fn($state) => $state->getLabel())
                                                ->color(fn($state) => match ($state) {
                                                    ProjectStatus::DRAFT => 'gray',
                                                    ProjectStatus::PREPARATION => 'warning',
                                                    ProjectStatus::STARTED => 'success',
                                                    ProjectStatus::FINISHED => 'info',
                                                    ProjectStatus::CANCELLED => 'danger',
                                                })
                                                ->icon(fn($state) => $state->getIcon()),

                                            TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y à H:i')
                                                ->icon('heroicon-o-calendar'),
                                        ]),
                                    ]),
                            ]),

                        Section::make('Planification')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('planned_start_date')
                                            ->label('Démarrage prévu')
                                            ->date('d/m/Y')
                                            ->placeholder('Non défini')
                                            ->icon('heroicon-o-calendar-days')
                                            ->color('warning'),

                                        TextEntry::make('planned_end_date')
                                            ->label('Fin prévue')
                                            ->date('d/m/Y')
                                            ->placeholder('Non définie')
                                            ->icon('heroicon-o-calendar-days')
                                            ->color('warning'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('started_at')
                                            ->label('Démarrage effectif')
                                            ->dateTime('d/m/Y à H:i')
                                            ->placeholder('Pas encore démarré')
                                            ->icon('heroicon-o-play-circle')
                                            ->color('success'),

                                        TextEntry::make('ended_at')
                                            ->label('Fin effective')
                                            ->dateTime('d/m/Y à H:i')
                                            ->placeholder('Pas encore terminé')
                                            ->icon('heroicon-o-check-circle')
                                            ->color('info'),
                                    ]),

                                TextEntry::make('duration')
                                    ->label('Durée réalisée')
                                    ->state(function ($record): string {
                                        if (!$record->started_at) {
                                            return 'Chantier non démarré';
                                        }

                                        $end = $record->ended_at ?? now();
                                        $duration = $record->started_at->diffInDays($end);

                                        return $duration . ' jour' . ($duration > 1 ? 's' : '');
                                    })
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-clock')
                                    ->visible(fn($record) => $record->started_at !== null),
                            ]),
                    ]),

                Section::make('Analyse Financière')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                // Vendu
                                Group::make([
                                    TextEntry::make('quoted_amount')
                                        ->label('Montant Vendu HT')
                                        ->money('EUR')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->color('primary')
                                        ->icon('heroicon-o-currency-euro'),

                                    TextEntry::make('estimated_cost')
                                        ->label('Coût prévisionnel (Étude)')
                                        ->money('EUR')
                                        ->color('gray')
                                        ->size('sm'),
                                ]),

                                // Déboursé réel
                                TextEntry::make('actual_debourse')
                                    ->label('Déboursé Réel')
                                    ->state(function ($record): float {
                                        $service = app(FinancialService::class);

                                        return $service->getActualTotalDebourse($record);
                                    })
                                    ->money('EUR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('warning')
                                    ->icon('heroicon-o-banknotes'),

                                // Marge brute
                                TextEntry::make('margin')
                                    ->label('Marge Brute')
                                    ->state(function ($record): float {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['margin_cents'];
                                    })
                                    ->money('EUR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['margin_cents'] >= 0 ? 'success' : 'danger';
                                    })
                                    ->icon('heroicon-o-chart-bar'),

                                // Taux de marge
                                TextEntry::make('margin_percentage')
                                    ->label('Taux de Marge')
                                    ->state(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return number_format($metrics['margin_percentage'], 1) . ' %';
                                    })
                                    ->badge()
                                    ->size('lg')
                                    ->color(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);
                                        $margin = $metrics['margin_percentage'];

                                        return match (true) {
                                            $margin >= 20 => 'success',
                                            $margin >= 15 => 'warning',
                                            $margin >= 10 => 'danger',
                                            default => 'gray',
                                        };
                                    })
                                    ->icon('heroicon-o-chart-pie'),
                            ]),

                        Grid::make()
                            ->schema([
                                TextEntry::make('is_within_estimate')
                                    ->label('Respect du budget étude')
                                    ->state(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['is_within_estimate'] ? 'Objectif atteint ✓' : 'Dépassement budgétaire';
                                    })
                                    ->badge()
                                    ->color(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['is_within_estimate'] ? 'success' : 'danger';
                                    })
                                    ->icon(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['is_within_estimate'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle';
                                    }),

                                TextEntry::make('variance_from_estimate')
                                    ->label('Écart vs Étude')
                                    ->state(function ($record): float {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['variance_from_estimate'];
                                    })
                                    ->money('EUR')
                                    ->badge()
                                    ->color(function ($record): string {
                                        $service = app(FinancialService::class);
                                        $metrics = $service->getProfitabilityMetrics($record);

                                        return $metrics['variance_from_estimate'] >= 0 ? 'success' : 'danger';
                                    })
                                    ->formatStateUsing(function ($state): string {
                                        $prefix = $state >= 0 ? '+' : '';

                                        return $prefix . number_format($state, 2, ',', ' ') . ' €';
                                    }),
                            ]),
                    ])
                    ->headerActions([
                        Action::make('generate_debourse_report')
                            ->label('Rapport Déboursé')
                            ->icon('heroicon-o-document-chart-bar')
                            ->color('primary')
                            ->action(function ($record) {
                                $service = app(DocumentService::class);
                                $path = $service->generateDebourseReport($record);

                                Notification::make()
                                    ->title('Rapport généré')
                                    ->success()
                                    ->send();

                                return response()->download($path);
                            }),
                    ]),

                Section::make('Ventilation du déboursé')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        RepeatableEntry::make('cost_breakdown')
                            ->label('')
                            ->state(function ($record): array {
                                $service = app(FinancialService::class);
                                $breakdown = $service->getDebourseBreakdown($record);

                                return $breakdown->filter(fn($item) => $item['total'] > 0)->map(function ($item, $key) {
                                    return [
                                        'label' => $item['label'],
                                        'total' => $item['total'],
                                    ];
                                })->values()->toArray();
                            })
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('label')
                                            ->label('Type de coût')
                                            ->weight('medium'),

                                        TextEntry::make('total')
                                            ->label('Montant')
                                            ->money('EUR')
                                            ->alignEnd()
                                            ->weight('bold')
                                            ->color('primary'),
                                    ]),
                            ])
                            ->contained(false)
                            ->columnSpanFull(),
                    ]),

                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Statistiques Projet')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('fabrications_count')
                                    ->label('Fabrications')
                                    ->state(fn($record) => $record->fabrications()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-wrench-screwdriver'),

                                TextEntry::make('costs_count')
                                    ->label('Lignes de coûts')
                                    ->state(fn($record) => $record->costs()->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-banknotes'),

                                TextEntry::make('reports_count')
                                    ->label('Procès-Verbaux')
                                    ->state(fn($record) => $record->reports()->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-document-check'),
                            ]),

                        Section::make('Métadonnées')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Date de création')
                                    ->dateTime('d/m/Y à H:i')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('updated_at')
                                    ->label('Dernière modification')
                                    ->dateTime('d/m/Y à H:i')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ]),

            ]);
    }
}
