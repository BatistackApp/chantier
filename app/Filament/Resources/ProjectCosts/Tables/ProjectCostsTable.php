<?php

namespace App\Filament\Resources\ProjectCosts\Tables;

use App\Enums\CostType;
use App\Models\ProjectCost;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectCostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucun coûts enregistré en base de donnée')
            ->emptyStateIcon(Heroicon::Banknotes)
            ->emptyStateActions([
                CreateAction::make()
                    ->icon(Heroicon::PlusCircle)
                    ->label('Nouveau couts'),
            ])
            ->defaultSort('spent_at', 'desc')
            ->persistSortInSession()
            ->persistFiltersInSession()
            ->columns([
                TextColumn::make('project.reference')
                    ->label('Projet')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (ProjectCost $record): string => route('filament.admin.resources.projects.view', ['record' => $record->project_id])),

                TextColumn::make('cost_type')
                    ->badge()
                    ->label('Type')
                    ->formatStateUsing(fn (CostType $state) => $state->getLabel())
                    ->colors([
                        'primary' => CostType::LABOR,
                        'success' => CostType::MATERIAL,
                        'warning' => CostType::MANUFACTURING,
                        'info' => CostType::RENTAL,
                        'danger' => CostType::SUBCONTRACTING,
                    ]),

                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable()
                    ->weight('bold')
                    ->alignEnd()
                    ->summarize([
                        Sum::make()
                            ->money('EUR')
                            ->label('Total'),
                    ]),

                TextColumn::make('spent_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('cost_type')
                    ->label('Type de coût')
                    ->options(CostType::class)
                    ->multiple(),

                SelectFilter::make('project')
                    ->relationship('project', 'reference')
                    ->searchable()
                    ->preload()
                    ->label('Chantier'),

                Filter::make('spent_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Du'),
                        DatePicker::make('to')
                            ->label('Au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('spent_at', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('spent_at', '<=', $data['to']));
                    }),

                Filter::make('high_cost')
                    ->label('Montants > 1000€')
                    ->query(fn ($query) => $query->where('amount', '>', 1000)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
