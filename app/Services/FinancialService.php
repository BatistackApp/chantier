<?php

namespace App\Services;

use App\Enums\CostType;
use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * Service de gestion financière (Déboursé et DGD)
 */
class FinancialService
{
    /**
     * Calcule le montant du compte prorata (basé sur le montant devisé)
     */
    public function calculateProrataAmount(Project $project): int
    {
        return (int) ($project->quoted_amount * 0.03);
    }

    /**
     * Calcule le déboursé total (réel) d'un projet.
     */
    public function getActualTotalDebourse(Project $project): int
    {
        return $this->getDebourseBreakdown($project)->sum('total');
    }

    /**
     * Fournit une ventilation détaillée du déboursé par type de coût.
     * Utile pour l'analyse de rentabilité "Étude vs Réel".
     */
    public function getDebourseBreakdown(Project $project): Collection
    {
        // 1. Coûts directs (Main d'œuvre chantier, Matériaux, Location, Sous-traitance)
        $directCosts = $project->costs()
            ->selectRaw('cost_type, SUM(amount) as total')
            ->groupBy('cost_type')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->getRawOriginal('cost_type') => (float) $row->total,
            ]);

        // 2. Coûts de fabrication (Atelier)
        $manufacturingTotal = (int) $project->fabrications()
            ->get()
            ->sum(fn ($fab) => $fab->quantity * $fab->unit_cost);

        // 3. Coûts de quincaillerie
        $quincaillerieTotal = (float) $project->fabrications()
            ->get()
            ->sum(fn ($fab) => $fab->items()->sum('unit_cost') * $fab->items()->sum('quantity'));

        // 4. Calcul du compte prorata (3%)
        $prorataAmount = $this->calculateProrataAmount($project);

        // On fusionne tout dans une collection structurée par l'Enum CostType
        return collect(CostType::cases())->mapWithKeys(function ($type) use ($directCosts, $manufacturingTotal, $quincaillerieTotal) {
            $total = $directCosts->get($type->value, 0);

            // On ajoute les spécificités atelier au type MANUFACTURING
            if ($type === CostType::MANUFACTURING) {
                $total += ($manufacturingTotal + $quincaillerieTotal);
            }

            return [$type->name => [
                'label' => $type->getLabel(),
                'total' => (int) $total,
            ]];
        })->put('PRORATA', [
            'label' => 'Compte Prorata (3%)',
            'total' => $prorataAmount,
        ]);
    }

    /**
     * Calcule la rentabilité en temps réel.
     */
    public function getProfitabilityMetrics(Project $project): array
    {
        $actualDebourse = $this->getActualTotalDebourse($project);
        $quotedAmount = $project->quoted_amount;
        $margin = $quotedAmount - $actualDebourse;

        return [
            'quoted_amount' => $quotedAmount,
            'actual_debourse' => $actualDebourse,
            'margin_cents' => $margin,
            'margin_percentage' => $quotedAmount > 0 ? round(($margin / $quotedAmount), 2) * 100 : 0,
            'is_within_estimate' => $actualDebourse <= $project->estimated_cost,
            'variance_from_estimate' => $project->estimated_cost - $actualDebourse,
        ];
    }

    /**
     * Prépare le Décompte Général Définitif (DGD).
     * Le DGD est le document final contractuel.
     */
    public function prepareDgdData(Project $project): array
    {
        $metrics = $this->getProfitabilityMetrics($project);

        // Dans un vrai flux BTP, on ajouterait ici :
        // - Travaux supplémentaires (TS)
        // - Retenues de garantie (souvent 5%)
        // - Compte prorata

        return [
            'project_info' => [
                'reference' => $project->reference,
                'title' => $project->title,
                'client' => $project->customer->name,
            ],
            'financials' => $metrics,
            'breakdown' => $this->getDebourseBreakdown($project),
            'prorata_deduction' => $this->calculateProrataAmount($project),
            'generated_at' => now()->format('d/m/Y'),
        ];
    }
}
