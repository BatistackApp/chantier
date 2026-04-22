<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Services\DocumentService;
use App\Services\FinancialService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucun chantier disponible dans la base de donnée')
            ->emptyStateIcon(Phosphor::CraneTower)
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Ajouter un chantier')
                    ->icon(Phosphor::PlusCircle),
            ])
            ->columns([
                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Référence copiée'),

                TextColumn::make('title')
                    ->label('Chantier')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('customer.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('Statut'),

                TextColumn::make('quoted_amount')
                    ->label('Vendu HT')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('margin_percentage')
                    ->label('Marge %')
                    ->state(function (Project $record): float {
                        $service = app(FinancialService::class);

                        return $service->getProfitabilityMetrics($record)['margin_percentage'];
                    })
                    ->badge()
                    ->color(fn (float $state) => match (true) {
                        $state >= 20 => 'success',
                        $state >= 15 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (float $state) => number_format($state, 1).'%')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('planned_start_date')
                    ->label('Démarrage Prévu')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Crée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(ProjectStatus::class)
                    ->multiple(),

                SelectFilter::make('customer.name')
                    ->relationship('customer', 'name')
                    ->label('Client')
                    ->searchable()
                    ->preload(),

                Filter::make('low_margin')
                    ->label('Marge critique (< 15%)')
                    ->query(function ($query) {
                        return $query;
                    }),

                Filter::make('dates')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Du'),

                        DatePicker::make('to')
                            ->label('Au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('planned_start_date', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('planned_start_date', '<=', $data['to']));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('generate_dgd')
                    ->label('Générer DGD')
                    ->icon(Heroicon::DocumentText)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Project $record) => $record->status === ProjectStatus::FINISHED)
                    ->action(function (Project $record) {
                        $service = app(DocumentService::class);
                        $path = $service->generateDgd($record);

                        Notification::make()
                            ->title('DGD généré avec succès')
                            ->success()
                            ->send();

                        return response()->download($path);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
