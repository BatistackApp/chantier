<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

/**
 * Service de génération documentaire
 */
class DocumentService
{
    /**
     * Génère un PDF à partir d'une vue Blade utilisant Tailwind CSS.
     */
    private function generatePdf(string $view, array $data, string $filename): string
    {
        $html = View::make($view, $data)->render();

        // On utilise Browsershot pour transformer le HTML en PDF
        // On inclut Tailwind via CDN dans la vue Blade ou un script de compilation
        $pdfContent = Browsershot::html($html)
            ->setNodeBinary(config('browsershot.node_binary_path'))
            ->setNpmBinary(config('browsershot.npm_binary_path'))
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();

        $directory = storage_path('app/public/documents');

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = "{$directory}/{$filename}.pdf";
        file_put_contents($path, $pdfContent);

        return $path;
    }

    /**
     * Génère la Fiche de Préparation Fabrication (Modèle Fabrication joint).
     */
    public function generateFabricationSheet(Project $project): string
    {
        $data = [
            'project' => $project,
            'fabrications' => $project->fabrications()->get(),
            'title' => "FICHE DE PRÉPARATION FABRICATION - {$project->reference}",
        ];

        return $this->generatePdf('pdf.fabrication_sheet', $data, "fab_{$project->reference}");
    }

    /**
     * Génère la Fiche de Préparation Chantier (Loi 1975, Logistique, Levage).
     */
    public function generatePreparationSheet(Project $project): string
    {
        $data = [
            'project' => $project,
            'prep' => $project->preparation,
            'title' => "FICHE DE PRÉPARATION CHANTIER - {$project->reference}",
        ];

        return $this->generatePdf('pdf.preparation_sheet', $data, "prep_{$project->reference}");
    }

    /**
     * Génère le PV de démarrage (Supports, Accès, Électricité).
     */
    public function generateStartReport(Project $project): string
    {
        $report = $project->reports()->where('type', 'start')->latest()->first();

        $data = [
            'project' => $project,
            'report' => $report,
            'title' => "PV DE CONSTAT DE DÉMARRAGE - {$project->reference}",
        ];

        return $this->generatePdf('pdf.project_report_start', $data, "pv_start_{$project->reference}");
    }

    /**
     * Génère le PV de fin (Conformité, Nettoyage, Réserves).
     */
    public function generateEndReport(Project $project): string
    {
        $report = $project->reports()->where('type', 'end')->latest()->first();

        $data = [
            'project' => $project,
            'report' => $report,
            'title' => "PV DE CONSTAT DE FIN DE TRAVAUX - {$project->reference}",
        ];

        return $this->generatePdf('pdf.project_report_end', $data, "pv_end_{$project->reference}");
    }

    /**
     * Génère le Décompte Général Définitif (DGD).
     */
    public function generateDgd(Project $project): string
    {
        $financialService = app(FinancialService::class);
        $dgdData = $financialService->prepareDgdData($project);

        return $this->generatePdf('pdf.project_dgd', [
            'project' => $project,
            'dgd' => $dgdData,
            'title' => "DGD - {$project->reference}",
        ], "dgd_{$project->reference}");
    }

    public function generateDebourseReport(Project $project): string
    {
        $financialService = app(FinancialService::class);
        $metrics = $financialService->getProfitabilityMetrics($project);

        $breakdown = $financialService->getDebourseBreakdown($project);

        return $this->generatePdf('pdf.project_debourse_report', [
            'project' => $project,
            'metrics' => $metrics,
            'breakdown' => $breakdown,
            'title' => "RAPPORT DÉBOURSÉ - {$project->reference}",
        ], "report_debourse_{$project->reference}");
    }
}
