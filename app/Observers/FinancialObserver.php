<?php

namespace App\Observers;

use App\Enums\ProjectStatus;
use App\Jobs\GenerateProjectDgdJob;
use App\Mail\LowProfitabilityAlertMail;
use App\Models\Project;
use App\Services\FinancialService;
use Illuminate\Support\Facades\Mail;

class FinancialObserver
{
    public function __construct(
        protected FinancialService $financialService,
    ) {}

    public function saved($model): void
    {
        $project = ($model instanceof Project) ? $model : $model->project;

        if (! $project) {
            return;
        }

        $metrics = $this->financialService->getProfitabilityMetrics($project);

        if ($metrics['margin_percentage'] < 15 && $project->status->value !== ProjectStatus::DRAFT->value) {
            Mail::to(config('mail.from.address'))->send(new LowProfitabilityAlertMail($project, $metrics['margin_percentage']));
        }

        if ($model instanceof Project && $model->wasChanged('status') && $model->status->value === ProjectStatus::FINISHED->value) {
            GenerateProjectDgdJob::dispatch($project);
        }
    }

    public function deleted($model): void
    {
        $this->saved($model);
    }
}
