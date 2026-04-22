<?php

namespace App\Observers;

use App\Models\Fabrication;
use App\Services\FinancialService;

class FabricationObserver
{
    public function __construct(
        protected FinancialService $financialService,
    ) {}

    public function saved(Fabrication $fabrication): void
    {
        $this->financialService->getProfitabilityMetrics($fabrication->project);
    }

    public function deleted(Fabrication $fabrication): void
    {
        $this->financialService->getProfitabilityMetrics($fabrication->project);
    }
}
