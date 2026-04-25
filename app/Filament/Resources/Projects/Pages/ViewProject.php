<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\ProjectReportType;
use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Services\DocumentService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
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
                ->schema(self::prepareSchema())
                ->slideOver()
                ->visible(fn (Project $record) => $record->status === ProjectStatus::DRAFT)
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

            Action::make('print_prepare')
                ->iconButton()
                ->color('primary')
                ->icon(Phosphor::Printer)
                ->tooltip('Imprimer Fiche Préparation')
                ->action(function (Project $record) {
                    $print = app(DocumentService::class)->generatePreparationSheet($record);

                    return response()->download($print);
                })
                ->visible(fn (Project $record) => $record->status === ProjectStatus::PREPARATION),

            Action::make('started')
                ->iconButton()
                ->color('warning')
                ->icon(Phosphor::Play)
                ->tooltip('Démarrer le chantier')
                ->schema(self::startedSchema())
                ->slideOver()
                ->action(function (Project $record, array $data) {
                    $record->reports()
                        ->create($data);

                    $record->updateQuietly(['status' => ProjectStatus::STARTED, 'started_at' => now()]);

                    Notification::make()
                        ->success()
                        ->title('Le PV de démarrage à été créer avec succès')
                        ->send();
                })
                ->visible(fn (Project $record) => $record->status === ProjectStatus::PREPARATION),

            Action::make('finished')
                ->iconButton()
                ->color('danger')
                ->icon(Phosphor::Stop)
                ->tooltip('Terminer le chantier')
                ->schema(self::endedSchema())
                ->slideOver()
                ->action(function (Project $record, array $data) {
                    $record->reports()
                        ->create($data);

                    $record->updateQuietly(['status' => ProjectStatus::FINISHED, 'ended_at' => now()]);

                    Notification::make()
                        ->success()
                        ->title('Le PV de Fin de chantier à été créer avec succès')
                        ->send();
                })
                ->visible(fn (Project $record) => $record->status === ProjectStatus::STARTED),

        ];
    }

    protected static function prepareSchema(): array
    {
        return [
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
                                        ->label('Fournisseur'),
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
        ];
    }

    protected static function startedSchema(): array
    {
        return [
            Section::make('Réception des supports')
                ->columnSpanFull()
                ->schema([
                    Toggle::make('supports_conformity')
                        ->label('Attentes et platines conformes aux plans')
                        ->default(true)
                        ->inline(false),

                    Toggle::make('access_ok')
                        ->label('Zone accessible (Camions / Levage)')
                        ->default(true)
                        ->inline(false),

                    Toggle::make('electricity_ok')
                        ->label('Points électriques opérationnels')
                        ->default(false)
                        ->inline(false),

                    Textarea::make('support_deviations')
                        ->label('Écarts constatés')
                        ->rows(3)
                        ->placeholder('Décrire les anomalies relevées...')
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Signature')
                ->columnSpanFull()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('signatory_name')
                                ->label('Nom du signataire (Client)')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Représentant entreprise cliente'),

                            DateTimePicker::make('signed_at')
                                ->label('Date et heure de signature')
                                ->required()
                                ->native(false)
                                ->default(now()),
                        ]),

                    SignaturePad::make('signature')
                        ->label(__('Signature du client'))
                        ->dotSize(2.0)
                        ->lineMinWidth(0.5)
                        ->lineMaxWidth(2.5)
                        ->throttle(16)
                        ->minDistance(5)
                        ->velocityFilterWeight(0.7),
                ]),

            Hidden::make('project_id')
                ->default(fn (Project $record) => $record->id),
            Hidden::make('type')->default(ProjectReportType::Start->value),
        ];
    }

    protected static function endedSchema(): array
    {
        return [
            Hidden::make('project_id')->default(fn (Project $record) => $record->id),
            Hidden::make('type')->default(ProjectReportType::End->value),

            Section::make('État d\'achèvement')
                ->columnSpanFull()
                ->schema([
                    Toggle::make('is_completed')
                        ->label('Travaux achevés conformément au contrat')
                        ->default(false)
                        ->inline(false),

                    Toggle::make('cleaning_done')
                        ->label('Nettoyage de la zone effectué')
                        ->default(false)
                        ->inline(false),

                    Repeater::make('reserves')
                        ->label('Réserves éventuelles')
                        ->schema([
                            Textarea::make('description')
                                ->label('Description de la réserve')
                                ->required()
                                ->rows(2),
                        ])
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter une réserve')
                        ->collapsible(),
                ])
                ->columns(2),

            Section::make('Signature')
                ->columnSpanFull()
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('signatory_name')
                                ->label('Nom du signataire (Client)')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Représentant entreprise cliente'),

                            DateTimePicker::make('signed_at')
                                ->label('Date et heure de signature')
                                ->required()
                                ->native(false)
                                ->default(now()),
                        ]),

                    SignaturePad::make('signature')
                        ->label(__('Signature du client'))
                        ->dotSize(2.0)
                        ->lineMinWidth(0.5)
                        ->lineMaxWidth(2.5)
                        ->throttle(16)
                        ->minDistance(5)
                        ->velocityFilterWeight(0.7),
                ]),
        ];
    }
}
