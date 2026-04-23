<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;
    protected static ?string $title = 'Éditer un client';
    protected static ?string $breadcrumb = 'Éditer un client';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::Eye)
                ->label('Fiche client'),
            DeleteAction::make()
                ->icon(Heroicon::Trash)
                ->label('Supprimer client'),
        ];
    }
}
