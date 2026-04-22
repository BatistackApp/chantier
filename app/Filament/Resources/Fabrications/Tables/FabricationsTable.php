<?php

namespace App\Filament\Resources\Fabrications\Tables;

use App\Enums\FabricationType;
use App\Models\Fabrication;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FabricationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('30s')
            ->emptyStateHeading('Aucun Ordre de fabrication en base de donnée')
            ->emptyStateIcon(Heroicon::WrenchScrewdriver)
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Nouvelle fabrication')
                    ->icon(Heroicon::PlusCircle),
            ])
            ->columns([
                TextColumn::make('project.reference')
                    ->label('Chantier')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (Fabrication $record): string => route('filament.admin.resources.projects.view', ['record' => $record->project_id])),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('label')
                    ->label('Désignation')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('dimensions')
                    ->label('Dim.')
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->label('Qté')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),

                TextColumn::make('color_code')
                    ->label('Couleur')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('unit_cost')
                    ->label('P.U.')
                    ->money('EUR', divideBy: 1)
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('total_cost')
                    ->label('Total')
                    ->state(fn (Fabrication $record): float => $record->quantity * $record->unit_cost)
                    ->money('EUR', divideBy: 1)
                    ->weight('bold')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('time_realized')
                    ->label('Temps réalisé')
                    ->time('H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(FabricationType::class)
                    ->multiple(),

                SelectFilter::make('project')
                    ->relationship('project', 'reference')
                    ->searchable()
                    ->preload()
                    ->label('Chantier'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
