<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Rules\SiretSirenIsExistRule;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Raison Social / Nom')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->required(),

                        Toggle::make('is_professional')
                            ->label('Client Professionnel')
                            ->default(true)
                            ->live()
                            ->helperText('Active les champs SIRET pour les professionnels'),

                        TextInput::make('siret')
                            ->label('Siret')
                            ->maxLength(14)
                            ->rules([
                                'nullable',
                                'regex:/^[0-9]{14}$/',
                            ])
                            ->visible(fn (Get $get) => $get('is_professional')),
                    ]),

                Section::make('Contact')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon(Heroicon::Envelope)
                            ->required(),

                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->prefixIcon(Heroicon::Phone)
                            ->mask('99 99 99 99 99'),
                    ])
                    ->columns(2),

                Section::make('Adresse')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('address')
                            ->label('Adresse Postal')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Numéro et nom de la voie')
                            ->required(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('postal_code')
                                    ->label('Code Postal')
                                    ->required(),

                                TextInput::make('city')
                                    ->label('Ville')
                                    ->required(),

                                TextInput::make('country')
                                    ->label('Pays')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
