@component('mail::message')
# ⚠️ Alerte de Rentabilité Critique

Le projet {{ $project->reference }} - {{ $project->title }} vient de subir une mise à jour financière qui a dégradé sa rentabilité.

** Métriques actuelles **
- ** Vendu: ** {{ \Illuminate\Support\Number::currency($project->quoted_amount, 'EUR') }}
- ** Déboursé réel: ** {{ \Illuminate\Support\Number::currency($project->actual_debourse, 'EUR') }}
- ** Marge actuelle: ** {{ $marginPercentage }} %

@component('mail::button', ['url' => '/admin/project/'.$project->id])
    Consulter le Déboursé Global
@endcomponent
*Cette alerte automatique est générée car la marge brute est passée sous le seuil de sécurité de 15%.*
*Une révision des coûts de main d'œuvre ou des fournitures est préconisée.*

Cordialement,<br>
{{ config('app.name') }}
@endcomponent
