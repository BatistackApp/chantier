<?php

namespace App\Filament\Resources\Fabrications\Schemas;

use App\Enums\FabricationType;
use App\Enums\ProjectStatus;
use App\Models\Fabrication;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FabricationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Projet Associé')
                            ->schema([
                                Group::make([
                                    TextEntry::make('project.reference')
                                        ->label('Référence Projet')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->copyable()
                                        ->url(fn ($record) => route('filament.admin.resources.projects.view', ['record' => $record->project_id]))
                                        ->color('primary')
                                        ->icon('heroicon-o-building-office-2'),

                                    TextEntry::make('project.title')
                                        ->label('Désignation')
                                        ->columnSpanFull(),

                                    TextEntry::make('project.customer.name')
                                        ->label('Client')
                                        ->icon('heroicon-o-user-group')
                                        ->url(fn ($record) => route('filament.admin.resources.customers.view', ['record' => $record->project->customer_id]))
                                        ->color('gray'),
                                ]),
                                Group::make([
                                    TextEntry::make('project.status')
                                        ->label('Statut Projet')
                                        ->badge(),
                                ]),
                            ]),

                        Section::make('Détails Fabrication')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('type')
                                            ->label('Type de fabrication')
                                            ->badge()
                                            ->size('lg')
                                            ->formatStateUsing(fn ($state) => $state->getLabel())
                                            ->color('primary')
                                            ->icon('heroicon-o-wrench-screwdriver'),

                                        TextEntry::make('dimensions')
                                            ->label('Dimensions')
                                            ->placeholder('Non spécifié')
                                            ->badge()
                                            ->color('gray')
                                            ->icon('heroicon-o-arrows-pointing-out'),

                                        TextEntry::make('color_code')
                                            ->label('Code couleur')
                                            ->placeholder('Non spécifié')
                                            ->badge()
                                            ->extraAttributes(fn (string $state, $record) => [
                                                'style' => "background-color: var(--color-ral-{$record->color_code}); color: white;",
                                            ])
                                            ->icon('heroicon-o-paint-brush')
                                            ->formatStateUsing(fn (?string $state): string => $state ? "RAL {$state}" : 'Non défini'),
                                    ]),

                                TextEntry::make('label')
                                    ->label('Désignation complète')
                                    ->size('md')
                                    ->weight('bold')
                                    ->columnSpanFull()
                                    ->icon('heroicon-o-tag'),
                            ]),
                    ]),

                Section::make('Analyse Economique')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Group::make([
                                    TextEntry::make('quantity')
                                        ->label('Quantité')
                                        ->numeric(decimalPlaces: 2)
                                        ->size('lg')
                                        ->weight('bold')
                                        ->color('info')
                                        ->icon('heroicon-o-cube'),

                                    TextEntry::make('unit_type')
                                        ->label('Unité')
                                        ->state(function ($record): string {
                                            // Déduction de l'unité selon le type
                                            return match ($record->type) {
                                                FabricationType::FOLDING => 'pièce(s)',
                                                FabricationType::HARDWARE => 'unité(s)',
                                                FabricationType::ASSEMBLY => 'ensemble(s)',
                                                default => 'unité(s)',
                                            };
                                        })
                                        ->size('lg')
                                        ->color('gray'),
                                ]),

                                TextEntry::make('unit_cost')
                                    ->label('Coût Unitaire')
                                    ->money('EUR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('warning')
                                    ->icon('heroicon-o-currency-euro'),

                                TextEntry::make('total_cost')
                                    ->label('Coût Total')
                                    ->state(fn ($record): float => $record->quantity * $record->unit_cost)
                                    ->money('EUR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success')
                                    ->icon('heroicon-o-calculator'),

                                Group::make([
                                    TextEntry::make('time_realized')
                                        ->label('Temps Réalisé')
                                        ->time('H:i')
                                        ->placeholder('Non renseigné')
                                        ->size('md')
                                        ->color('primary')
                                        ->icon('heroicon-o-clock'),

                                    TextEntry::make('productivity')
                                        ->label('Productivité')
                                        ->state(function ($record): string {
                                            if (! $record->time_realized) {
                                                return 'N/A';
                                            }

                                            $minutes = ($record->time_realized->hour * 60) + $record->time_realized->minute;
                                            if ($minutes === 0) {
                                                return 'N/A';
                                            }

                                            $piecesPerHour = ($record->quantity / $minutes) * 60;

                                            return number_format($piecesPerHour, 1, ',', ' ').' pcs/h';
                                        })
                                        ->badge()
                                        ->color('info')
                                        ->size('sm'),
                                ]),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cost_per_unit_metric')
                                    ->label('Coût moyen par unité')
                                    ->state(function ($record): string {
                                        if ($record->quantity == 0) {
                                            return 'N/A';
                                        }

                                        $avgCost = ($record->quantity * $record->unit_cost) / $record->quantity;

                                        return number_format($avgCost, 2, ',', ' ').' € / unité';
                                    })
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('project_contribution')
                                    ->label('Part dans le projet')
                                    ->state(function ($record): string {
                                        $totalFabCost = $record->project->fabrications()
                                            ->get()
                                            ->sum(fn ($f) => $f->quantity * $f->unit_cost);

                                        if ($totalFabCost == 0) {
                                            return '100 %';
                                        }

                                        $thisCost = $record->quantity * $record->unit_cost;
                                        $percentage = ($thisCost / $totalFabCost) * 100;

                                        return number_format($percentage, 1, ',', ' ').' %';
                                    })
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-chart-pie'),
                            ]),
                    ]),

                Section::make('Quincaillerie Associée')
                    ->collapsible()
                    ->collapsed(fn ($record) => $record->items()->count() === 0)
                    ->icon(Heroicon::Cog6Tooth)
                    ->headerActions([
                        Action::make('add_item')
                            ->label('Ajouter Quincaillerie')
                            ->icon('heroicon-o-plus-circle')
                            ->url(fn ($record) => route('filament.admin.resources.fabrications.edit', ['record' => $record]))
                            ->color('primary'),
                    ])
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('type')
                                            ->label('Type')
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('label')
                                            ->label('Désignation')
                                            ->weight('medium'),

                                        TextEntry::make('quantity')
                                            ->label('Quantité')
                                            ->numeric()
                                            ->alignEnd(),

                                        TextEntry::make('unit_cost')
                                            ->label('Coût unitaire')
                                            ->money('EUR')
                                            ->alignEnd(),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        TextEntry::make('items_total')
                            ->label('Total Quincaillerie')
                            ->state(function ($record): float {
                                return $record->items()->sum('unit_cost');
                            })
                            ->money('EUR')
                            ->weight('bold')
                            ->color('success')
                            ->icon('heroicon-o-banknotes'),
                    ]),

                Section::make('Traçabilité Atelier')
                    ->collapsible()
                    ->collapsed()
                    ->icon(Heroicon::ClipboardDocumentCheck)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Créée le')
                                    ->dateTime('d/m/Y à H:i')
                                    ->icon('heroicon-o-calendar')
                                    ->color('gray'),

                                TextEntry::make('updated_at')
                                    ->label('Modifiée le')
                                    ->dateTime('d/m/Y à H:i')
                                    ->since()
                                    ->icon('heroicon-o-clock')
                                    ->color('gray'),

                                TextEntry::make('status_indicator')
                                    ->label('État')
                                    ->state(function ($record): string {
                                        if ($record->time_realized) {
                                            return 'Réalisée ✓';
                                        }

                                        return match ($record->project->status) {
                                            ProjectStatus::PREPARATION => 'En préparation',
                                            ProjectStatus::STARTED => 'En cours',
                                            default => 'En attente',
                                        };
                                    })
                                    ->badge()
                                    ->color(function ($record): string {
                                        if ($record->time_realized) {
                                            return 'success';
                                        }

                                        return match ($record->project->status) {
                                            ProjectStatus::PREPARATION => 'warning',
                                            ProjectStatus::STARTED => 'info',
                                            default => 'gray',
                                        };
                                    })
                                    ->icon(function ($record): string {
                                        return $record->time_realized ? 'heroicon-o-check-circle' : 'heroicon-o-clock';
                                    }),
                            ]),
                    ]),
            ]);
    }
}
