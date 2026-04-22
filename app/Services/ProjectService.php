<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectReport;
use DB;

/**
 * Service de gestion du cycle de vie du projet
 */
class ProjectService
{
    /**
     * Prépare un chantier (Initialise la table de préparation)
     */
    public function prepareProject(Project $project): void
    {
        $project->update(['status' => ProjectStatus::PREPARATION]);
        $project->preparation()->firstOrCreate([]);
    }

    /**
     * Valide le démarrage d'un chantier via le PV
     */
    public function startProject(Project $project, array $pvData): void
    {
        DB::transaction(function () use ($project, $pvData) {
            $project->reports()->create(array_merge($pvData, ['type' => 'start']));
            $project->update([
                'status' => ProjectStatus::STARTED,
                'started_at' => now(),
            ]);
        });
    }

    /**
     * Initialise et valide la phase de préparation.
     * Basé sur "Modèle Préparation chantier.docx"
     */
    public function completePreparation(Project $project, array $data): void
    {
        DB::transaction(function () use ($project, $data) {
            $prep = $project->preparation()->updateOrCreate([], [
                'subcontractor_form_ok' => $data['subcontractor_form_ok'] ?? false,
                'subcontractor_contract_ok' => $data['subcontractor_contract_ok'] ?? false,
                'logistics_status' => $data['logistics_status'] ?? [],
                'lifting_means' => $data['lifting_means'] ?? null,
                'lifting_count' => $data['lifting_count'] ?? 0,
                'lifting_provider' => $data['lifting_provider'] ?? null,
                'safety_nets_required' => $data['safety_nets_required'] ?? false,
                'safety_nets_provider' => $data['safety_nets_provider'] ?? null,
                'observations' => $data['observations'] ?? null,
            ]);

            $project->update(['status' => ProjectStatus::PREPARATION]);
        });
    }

    /**
     * Valide le PV de démarrage (Démarrage effectif).
     * Basé sur "PV_constat_demarrage.docx"
     */
    public function validateStartReport(Project $project, array $reportData): ProjectReport
    {
        return DB::transaction(function () use ($project, $reportData) {
            // Création du PV
            $report = $project->reports()->create([
                'type' => 'start',
                'supports_conformity' => $reportData['supports_conformity'] ?? false,
                'support_deviations' => $reportData['support_deviations'] ?? null,
                'access_ok' => $reportData['access_ok'] ?? false,
                'electricity_ok' => $reportData['electricity_ok'] ?? false,
                'signed_at' => now(),
                'signatory_name' => $reportData['signatory_name'] ?? 'Client sur site',
            ]);

            // Mise à jour du chantier
            $project->update([
                'status' => ProjectStatus::STARTED,
                'planned_start_date' => $reportData['official_start_date'] ?? now(),
            ]);

            return $report;
        });
    }

    /**
     * Valide le PV de fin et gère la clôture.
     * Basé sur "PV_constat_fin.docx"
     */
    public function validateEndReport(Project $project, array $reportData): ProjectReport
    {
        return DB::transaction(function () use ($project, $reportData) {
            $report = $project->reports()->create([
                'type' => 'end',
                'is_completed' => $reportData['is_completed'] ?? false,
                'cleaning_done' => $reportData['cleaning_done'] ?? false,
                'reserves' => $reportData['reserves'] ?? [],
                'signed_at' => now(),
                'signatory_name' => $reportData['signatory_name'] ?? 'Client sur site',
            ]);

            // On ne passe en FINISHED que si l'achèvement est validé sans réserves critiques
            if ($report->is_completed && empty($report->reserves)) {
                $project->update(['status' => ProjectStatus::FINISHED]);
            }

            return $report;
        });
    }

    /**
     * Vérifie si le chantier est "Prêt pour l'Atelier".
     */
    public function isReadyForManufacturing(Project $project): bool
    {
        $prep = $project->preparation;
        if (! $prep) {
            return false;
        }

        // On exige au moins la conformité Loi 1975 pour lancer la fab
        return $prep->subcontractor_form_ok && $prep->subcontractor_contract_ok;
    }
}
