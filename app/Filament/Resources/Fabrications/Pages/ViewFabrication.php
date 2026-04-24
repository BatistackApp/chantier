<?php

namespace App\Filament\Resources\Fabrications\Pages;

use App\Filament\Resources\Fabrications\FabricationResource;
use App\Models\Fabrication;
use App\Services\DocumentService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewFabrication extends ViewRecord
{
    protected static string $resource = FabricationResource::class;
    protected static ?string $breadcrumb = 'Fiche Fabrication';
    protected static ?string $title = 'Fiche Fabrication';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::PencilSquare)
                ->label('Modifier Fabrication'),

            Action::make('print')
                ->iconButton()
                ->tooltip('Imprimer')
                ->icon(Heroicon::Printer)
                ->action(function (Fabrication $record) {
                    $service = app(DocumentService::class);
                    $path = $service->generateFabricationSheet($record->project);

                    return response()->download($path);
                }),
        ];
    }
}
