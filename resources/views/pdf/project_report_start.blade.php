@extends('pdf.layout')

@section('content')
    <!-- Header -->
    <div class="flex justify-between items-start border-b-4 border-black pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-black uppercase leading-none">Procès-Verbal de Constat</h1>
            <h2 class="text-xl font-bold text-slate-600 uppercase">Démarrage de Chantier</h2>
        </div>
        <div class="text-right">
            <div class="bg-black text-white px-4 py-1 font-bold text-sm mb-1">DOSSIER EXÉCUTION</div>
            <p class="font-mono text-lg font-bold">{{ $project->reference }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="col-span-2 border border-black p-3">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Chantier</span>
                    <span class="font-bold">{{ $project->title }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Date du constat</span>
                    <span class="font-bold">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Lieu</span>
                    <span class="font-bold italic">{{ $project->address ?? 'Sur site' }}</span>
                </div>
            </div>
        </div>
        <div class="border border-black p-3 bg-slate-50">
            <span class="text-[10px] font-bold uppercase text-slate-400 block mb-2">Parties Présentes</span>
            <ul class="text-[11px] space-y-1">
                <li><span class="font-bold">C2ME :</span> {{ auth()->user()->name ?? 'Chef d\'équipe' }}</li>
                <li><span class="font-bold">Client :</span> {{ $report->signatory_name ?? 'Anuri France' }}</li>
                <li class="border-t border-slate-200 mt-1 pt-1 text-slate-400">Autre : .........................</li>
            </ul>
        </div>
    </div>

    <div class="mb-6">
        <div class="bg-slate-800 text-white px-3 py-1 text-xs font-bold uppercase">Objet du Constat</div>
        <div class="border border-slate-800 p-3 italic text-slate-600">
            Le présent constat a pour objet de valider la mise à disposition des supports et des zones de travail nécessaires au lot de charpente métallique.
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <!-- Réception des supports -->
        <section class="border-2 border-black">
            <div class="bg-black text-white px-3 py-1 font-bold uppercase text-xs">Réception des Supports</div>
            <div class="p-3 space-y-4">
                <div class="flex items-start">
                    <div class="w-5 h-5 border-2 border-black mr-2 flex-shrink-0 flex items-center justify-center font-bold">
                        {{ ($report->supports_conformity ?? false) ? 'X' : '' }}
                    </div>
                    <span class="font-bold">Les attentes et platines sont conformes aux plans d’exécution.</span>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 mb-1 italic">Écarts ou Anomalies constatés :</p>
                    <div class="min-h-[80px] border border-slate-200 p-2 text-xs">
                        {{ $report->support_deviations ?? 'Néant.' }}
                    </div>
                </div>
            </div>
        </section>

        <!-- Logistique et Accès -->
        <section class="border-2 border-black">
            <div class="bg-black text-white px-3 py-1 font-bold uppercase text-xs">Logistique et Accès</div>
            <div class="p-3 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span>Zone accessible (Camions / Levage)</span>
                    <span class="font-bold border border-black px-2">{{ ($report->access_ok ?? false) ? 'OUI' : 'NON' }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span>Zone stabilisée mise à disposition</span>
                    <span class="font-bold border border-black px-2">OUI / NON</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span>Points électriques opérationnels</span>
                    <span class="font-bold border border-black px-2">{{ ($report->electricity_ok ?? false) ? 'OUI' : 'NON' }}</span>
                </div>

                <div class="bg-amber-50 p-2 border border-amber-200 text-[10px] leading-tight">
                    <strong>Rappel Météo :</strong> Seuil de 72 km/h pour l'arrêt du levage. Manipulation bardage déconseillée au delà de 45 km/h.
                </div>
            </div>
        </section>
    </div>

    <div class="mt-6 border-2 border-black">
        <div class="bg-slate-200 px-3 py-1 font-bold uppercase text-xs border-b-2 border-black">Décision et Engagement</div>
        <div class="p-4 grid grid-cols-2 gap-8">
            <div class="flex flex-col justify-center">
                <p class="text-sm font-bold uppercase">Date officielle de démarrage :</p>
                <p class="text-2xl font-black text-blue-800 underline decoration-slate-300">{{ $report->signed_at ? $report->signed_at->format('d / m / Y') : '.... / .... / 202...' }}</p>
            </div>
            <div class="bg-slate-50 p-3 border border-slate-200 italic text-[11px]">
                <p class="font-bold not-italic mb-1 underline">Réserves éventuelles :</p>
                @if($report->reserves)
                    <ul>
                        @foreach($report->reserves as $reserve)
                            <li>{{ $reserve['description'] }}</li>
                        @endforeach
                    </ul>
                @else
                    Aucune Réserves éventuelles
                @endif
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="mt-8 grid grid-cols-2 gap-10">
        <div class="border border-slate-300 p-4 h-32 relative">
            <span class="absolute top-2 left-2 text-[10px] font-bold uppercase text-slate-400 tracking-widest">Visa C2ME (Sous-Traitant)</span>
            <!-- Espace signature -->
        </div>
        <div class="border border-black p-4 h-32 relative bg-slate-50">
            <span class="absolute top-2 left-2 text-[10px] font-bold uppercase text-slate-800 tracking-widest">Visa Client (Anuri France)</span>
            <!-- Espace signature -->
        </div>
    </div>
    <div class="page-break"></div>
    <div class="mt-10 p-4 bg-slate-100 border-l-4 border-slate-400 break-inside-avoid">
        <h3 class="font-bold text-xs uppercase mb-2">Notice Interne : Points de Vigilance avant Démarrage</h3>
        <ul class="text-[10px] space-y-1 list-disc pl-4 text-slate-600 italic leading-snug">
            <li><strong>Traçabilité :</strong> Prenez une photo globale des supports avec un point fixe de référence.</li>
            <li><strong>Anomalie Maçonnerie :</strong> En cas de platine décalée (> 3cm), photo nette avec un mètre et appel immédiat au conducteur de travaux.</li>
            <li><strong>Sécurité :</strong> Ne commencez pas la pose avant d'avoir une instruction écrite si le défaut empêche le montage conforme.</li>
            <li><strong>Météo :</strong> Mentionnez les conditions si le vent est soutenu lors du constat.</li>
        </ul>
    </div>
@endsection
