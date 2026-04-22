<?php

namespace App\Console\Commands;

use App\Enums\ProjectStatus;
use App\Mail\LowProfitabilityAlertMail;
use App\Models\Project;
use App\Services\FinancialService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:project-health')]
#[Description('Vérifie la santé financière et administrative des chantiers actifs')]
class CheckProjectHealthCommand extends Command
{
    public function __construct(protected FinancialService $financialService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la vérification de santé...');

        $activeProjects = Project::where('status', [ProjectStatus::STARTED, ProjectStatus::PREPARATION])->get();

        foreach ($activeProjects as $project) {
            $metrics = $this->financialService->getProfitabilityMetrics($project);

            if ($metrics['margin_percentage'] < 10) {
                $this->warn("Chantier critique détecté: {$project->reference} ({$metrics['margin_percentage']}} %)");

                \Mail::to(config('mail.from.address'))->send(
                    new LowProfitabilityAlertMail($project, $metrics['margin_percentage'])
                );
            }
        }

        // 2. Détection des retards administratifs (ex: Démarré sans PV après 3 jours)
        $delayedProjects = Project::where('status', ProjectStatus::STARTED)
            ->where('created_at', '<', now()->subDays(3))
            ->whereDoesntHave('reports', fn ($q) => $q->where('type', 'start'))
            ->get();

        foreach ($delayedProjects as $project) {
            $this->error("Retard PV de démarrage : {$project->reference}");
            // Logique de notification spécifique ici
        }

        $this->info('Vérification terminée.');

    }
}
