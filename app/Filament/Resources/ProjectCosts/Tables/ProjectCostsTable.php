<?php

namespace App\Filament\Resources\ProjectCosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
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
            ->columns([

            ])
            ->filters([
                //
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
