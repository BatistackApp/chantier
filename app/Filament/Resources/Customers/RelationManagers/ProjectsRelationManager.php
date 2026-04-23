<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\ProjectStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Chantiers du client';

    protected static ?string $recordTitleAttribute = 'reference';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->columns([
                TextColumn::make('reference')
                    ->label('Référence')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('title')
                    ->label('Chantier')
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('quoted_amount')
                    ->label('Vendu HT')
                    ->money('EUR')
                    ->alignEnd(),

                TextColumn::make('planned_start_date')
                    ->label('Démarrage')
                    ->date('d/m/Y'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectStatus::class),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.projects.view', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
