<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Customer;
use App\Services\SirenService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $title = 'Fiche client';

    protected static ?string $breadcrumb = 'Fiche client';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::PencilSquare)
                ->label('Modifier client'),

            Action::make('verif_siren')
                ->icon(Heroicon::CheckBadge)
                ->label('Vérifier SIREN')
                ->visible(fn (Customer $record) => $record->is_professional)
                ->action(function (Customer $record) {
                    Cache::delete("insee_exists_{$record->siret}");
                    $api = app(SirenService::class);
                    $api->exists($record->siret);
                }),
        ];
    }
}
