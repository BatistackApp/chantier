<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;
    protected static ?string $breadcrumb = 'Fiche Chantier';
    protected static ?string $title = 'Fiche Chantier';


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::PencilSquare)
                ->label('Modifier chantier'),

            Action::make('prepare')
                ->iconButton()
                ->tooltip('Préparer chantier')
                ->icon(Phosphor::PlayCircle)
                ->color('warning')
                ->schema([

                ])
                ->action(function (Project $record, array $data) {
                    $record->update(['status' => ProjectStatus::PREPARATION->value]);

                    Notification::make()
                        ->title('Chantier passer en préparation')
                        ->success()
                        ->send();
                }),
        ];
    }
}
