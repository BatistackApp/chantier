<?php

namespace App\Observers;

use App\Jobs\UpdateLatLngProjectJob;
use App\Models\Project;
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
    }

    public function saved(Project $project): void
    {
        if ($project->wasChanged('address')) {
            UpdateLatLngProjectJob::dispatch($project);
        }
    }
}
