<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\ProjectReportType;
use App\Enums\ProjectStatus;
use App\Filament\Resources\ProjectReports\Schemas\ProjectReportForm;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
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
                    Section::make('Préparation du chantier')
                        ->columnSpanFull()
                        ->schema([
                            Section::make('Conformité Loi 1975')
                                ->columnSpanFull()
                                ->schema([
                                    Toggle::make('subcontractor_form_ok')
                                        ->label('Formulaire acceptation sous-traitant niveau 2'),

                                    Toggle::make('subcontractor_contract_ok')
                                        ->label('Contrat de sous-traitance'),
                                ]),

                            Section::make('Logistique')
                                ->columnSpanFull()
                                ->schema([
                                    CheckboxList::make('logistics_status')
                                        ->label('Etat des lieux')
                                        ->options([
                                            'terrasse' => 'Terrassement',
                                            'platine' => 'Platine/Plot Béton',
                                            'secure_charge' => 'Chargement/Déchargement Sécurisé',
                                        ]),

                                    Fieldset::make('Moyen de levage')
                                        ->schema([
                                            TextInput::make('lifting_count')
                                                ->label('Nombre'),

                                            TextInput::make('lifting_provider')
                                                ->label('Fournisseur')
                                        ]),
                                ]),

                            Section::make('Social et Securité')
                                ->columnSpanFull()
                                ->schema([
                                    Checkbox::make('safety_nets_required')
                                        ->label('Attestations fournies et en règles'),

                                    Checkbox::make('safety_nets_close')
                                        ->label('Pose des filets')
                                        ->live(),

                                    TextInput::make('safety_nets_provider')
                                        ->label('Pose des filets par')
                                        ->visible(fn (Get $get) => $get('safety_nets_close') === true),
                                ]),

                            Textarea::make('observations')
                                ->columnSpanFull()
                                ->label('Observations')
                                ->rows(3),
                        ])
                        ->columns(3),
                ])
                ->slideOver()
                ->action(function (Project $record, array $data) {
                    $record->update([
                        'status' => ProjectStatus::PREPARATION->value,
                    ]);

                    $record->preparation()->create($data);

                    Notification::make()
                        ->title('Chantier passer en préparation')
                        ->success()
                        ->send();
                }),
        ];
    }
}
