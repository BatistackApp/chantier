<?php

namespace App\Filament\Resources\ProjectReports\Tables;

use App\Enums\ProjectReportType;
use App\Models\ProjectReport;
use App\Services\DocumentService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucun Procès Verbal dans la base de donnée')
            ->emptyStateIcon(Heroicon::DocumentCheck)
            ->emptyStateActions([
                CreateAction::make()
                    ->icon(Heroicon::PlusCircle)
                    ->label('Nouveau PV'),
            ])
            ->defaultSort('signed_at', 'desc')
            ->columns([
                TextColumn::make('project.reference')
                    ->label('Projet')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (ProjectReport $record): string => route('filament.admin.resources.projects.view', ['record' => $record->project_id])),

                TextColumn::make('type')
                    ->badge()
                    ->label('Type PV')
                    ->formatStateUsing(fn (ProjectReportType $state) => $state->getLabel())
                    ->colors([
                        'info' => ProjectReportType::Start,
                        'success' => ProjectReportType::End,
                    ])
                    ->icons([
                        'heroicon-o-play' => ProjectReportType::Start,
                        'heroicon-o-check-circle' => ProjectReportType::End,
                    ]),

                IconColumn::make('supports_conformity')
                    ->label('Conformité')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->visible(fn () => request()->has('tableFilters.type.value')
                        && request('tableFilters.type.value') === ProjectReportType::Start->value),

                IconColumn::make('is_completed')
                    ->label('Achevé')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->visible(fn () => request()->has('tableFilters.type.value')
                        && request('tableFilters.type.value') === ProjectReportType::End->value),

                TextColumn::make('signed_at')
                    ->label('Signé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('has_reserves')
                    ->label('Réserves')
                    ->state(fn (ProjectReport $record): string => $record->reserves ? 'Oui ('.count($record->reserves).')' : 'Non')
                    ->badge()
                    ->color(fn (ProjectReport $record): string => $record->reserves ? 'warning' : 'success')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(ProjectReportType::class)
                    ->native(false),

                SelectFilter::make('project')
                    ->relationship('project', 'reference')
                    ->searchable()
                    ->preload()
                    ->label('Chantier'),

                TernaryFilter::make('supports_conformity')
                    ->label('Supports conformes')
                    ->placeholder('Tous')
                    ->trueLabel('Conformes')
                    ->falseLabel('Non conformes'),

                TernaryFilter::make('is_completed')
                    ->label('Travaux achevés')
                    ->placeholder('Tous')
                    ->trueLabel('Achevés')
                    ->falseLabel('En cours'),

                Filter::make('with_reserves')
                    ->label('Avec réserves')
                    ->query(fn ($query) => $query->whereNotNull('reserves')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('generate_pdf')
                    ->label('Générer PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (ProjectReport $record) {
                        $service = app(DocumentService::class);

                        $path = match ($record->type) {
                            ProjectReportType::Start => $service->generateStartReport($record->project),
                            ProjectReportType::End => $service->generateEndReport($record->project),
                        };

                        Notification::make()
                            ->title('PV généré avec succès')
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
