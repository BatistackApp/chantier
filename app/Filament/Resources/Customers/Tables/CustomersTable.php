<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucun client dans la base de donnée')
            ->emptyStateIcon(Heroicon::UserGroup)
            ->emptyStateActions([
                CreateAction::make()
                    ->icon(Heroicon::UserPlus)
                    ->label('Ajouter un client'),
            ])
            ->defaultSort('name', 'asc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->columns([
                TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Customer $record): string => $record->is_professional ? 'Professionnel' : 'Particulier'),

                IconColumn::make('is_professional')
                    ->label('Type')
                    ->boolean()
                    ->trueIcon(Heroicon::BuildingOffice)
                    ->falseIcon(Heroicon::User)
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->tooltip(fn (Customer $record): string => $record->is_professional ? 'Professionnel' : 'Particulier'),

                TextColumn::make('siret')
                    ->label('Siret')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Non Renseigné')
                    ->formatStateUsing(fn (?string $state = null): string => $state ? chunk_split($state, 3, ' ') : 'N/A'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copié')
                    ->icon(Heroicon::Envelope),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable()
                    ->icon(Heroicon::Phone),

                TextColumn::make('project_count')
                    ->label('Chantiers')
                    ->counts('projects')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_professional')
                    ->label('Type de client')
                    ->placeholder('Tous')
                    ->trueLabel('Professionnels')
                    ->falseLabel('Particuliers')
                    ->native(false),

                Filter::make('has_projects')
                    ->label('Avec chantiers actifs')
                    ->query(fn ($query) => $query->has('projects')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('create_project')
                    ->label('Nouveau chantiers')
                    ->icon(Heroicon::PlusCircle)
                    ->url(fn (Customer $record): string => route('filament.admin.resources.projects.create', [
                        'customer_id' => $record->id,
                    ])),
            ]);
    }
}
