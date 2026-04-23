<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use App\Services\SirenService;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations client')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Raison social / Nom'),

                        TextEntry::make('siret')
                            ->label('Siret')
                            ->iconColor(function (Customer $customer) {
                                $siretService = app(SirenService::class);

                                return $siretService->exists($customer->siret) ? 'success' : 'danger';
                            })
                            ->tooltip(function (Customer $customer) {
                                $siretService = app(SirenService::class);

                                return $siretService->exists($customer->siret) ? 'Siret Valide' : 'Siret Invalide';
                            })
                            ->icon(function (Customer $customer) {
                                $siretService = app(SirenService::class);
                                if ($siretService->exists($customer->siret)) {
                                    return Heroicon::CheckCircle;
                                } else {
                                    return Heroicon::XCircle;
                                }
                            }),

                        TextEntry::make('email')
                            ->label('Email'),

                        IconEntry::make('is_professional')
                            ->label('Entreprise ?'),

                        TextEntry::make('address')
                            ->label('Adresse Postale')
                            ->formatStateUsing(function (Customer $record) {
                                return new HtmlString("{$record->address} <br>{$record->postal_code} {$record->city}");
                            }),

                        TextEntry::make('phone')
                            ->label('Téléphone'),
                    ]),
            ]);
    }
}
