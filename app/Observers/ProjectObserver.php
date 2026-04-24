<?php

namespace App\Observers;

use App\Enums\ProjectStatus;
use App\Jobs\UpdateLatLngProjectJob;
use App\Models\Project;
use App\Services\DocumentService;
use App\Services\FinancialService;

class ProjectObserver
{
    public function __construct(
        protected FinancialService $financialService,
    ) {}

    public function updated(Project $project): void
    {
        if ($project->wasChanged('quoted_amount')) {
            $this->financialService->getProfitabilityMetrics($project);
        }

        if ($project->wasChanged('status')) {
            if ($project->status === ProjectStatus::PREPARATION) {
                app(DocumentService::class)->generatePreparationSheet($project);
            }
        }
    }

    public function saved(Project $project): void
    {
        if ($project->wasChanged('address')) {
            UpdateLatLngProjectJob::dispatch($project);
        }
    }
}
