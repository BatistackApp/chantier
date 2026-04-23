@extends('pdf.layout')

@section('content')
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-45 opacity-[0.03] text-8xl font-black pointer-events-none uppercase text-green-900">
        Travaux Terminés
    </div>

    <div class="flex justify-between items-start border-b-4 border-green-700 pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-black uppercase leading-none">Procès-Verbal de Constat</h1>
            <h2 class="text-xl font-bold text-green-700 uppercase tracking-tighter">Fin de Travaux / Achèvement</h2>
        </div>
        <div class="text-right">
            <div class="bg-green-700 text-white px-4 py-1 font-bold text-sm mb-1 uppercase">Livraison Chantier</div>
            <p class="font-mono text-lg font-bold">{{ $project->reference }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="col-span-2 border border-slate-300 p-3">
            <div class="grid grid-cols-2 gap-4 text-[11px]">
                <div>
                    <span class="text-[9px] font-bold uppercase text-slate-400 block">Désignation Chantier</span>
                    <span class="font-bold text-sm">{{ $project->title }}</span>
                </div>
                <div>
                    <span class="text-[9px] font-bold uppercase text-slate-400 block">Date du constat</span>
                    <span class="font-bold text-sm">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-[9px] font-bold uppercase text-slate-400 block">Lieu des travaux</span>
                    <span class="font-bold italic text-slate-600">{{ $project->address ?? 'Lieu d\'intervention contractuel' }}</span>
                </div>
            </div>
        </div>
        <div class="border border-slate-300 p-3 bg-slate-50">
            <span class="text-[9px] font-bold uppercase text-slate-400 block mb-2">Signataires Présents</span>
            <ul class="text-[10px] space-y-1">
                <li><span class="font-bold">Pour C2ME :</span> {{ auth()->user()->name ?? 'Responsable Chantier' }}</li>
                <li><span class="font-bold">Pour Client :</span> {{ $report->signatory_name ?? 'Représentant Client' }}</li>
            </ul>
        </div>
    </div>

    <div class="mb-6 p-3 border border-slate-200 bg-slate-50 italic text-slate-700">
        Les parties se sont réunies ce jour pour constater l'achèvement des prestations de fourniture et de montage de la charpente métallique confiées à C2ME.
    </div>

    <section class="border-2 border-black mb-6">
        <div class="bg-slate-800 text-white px-3 py-1 font-bold uppercase text-xs">État d'Avancement et Conformité</div>
        <div class="p-4 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="font-semibold">Achèvement des travaux prévus au contrat et avenants</span>
                <div class="flex gap-4">
                    <span class="flex items-center gap-1 font-bold italic underline">
                        <div class="w-4 h-4 border border-black flex items-center justify-center">{{ ($report->is_completed ?? false) ? 'X' : '' }}</div> OUI
                    </span>
                    <span class="flex items-center gap-1 opacity-40">
                        <div class="w-4 h-4 border border-black"></div> NON
                    </span>
                </div>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="font-semibold text-slate-700 italic">Ouvrages conformes aux plans de fabrication et notes de calcul</span>
                <span class="font-bold border border-black px-2 bg-slate-100">CONFORME</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="font-semibold">Nettoyage de la zone d'intervention effectué (chutes, déchets)</span>
                <span class="font-bold border border-black px-2">{{ ($report->cleaning_done ?? false) ? 'OUI' : 'NON' }}</span>
            </div>
        </div>
    </section>

    <section class="border-2 border-black mb-6">
        <div class="bg-slate-100 px-3 py-1 font-bold uppercase text-xs border-b-2 border-black italic">Prononcé du Constat</div>
        <div class="p-4">
            @php $hasReserves = !empty($report->reserves); @endphp
            <div class="flex items-start gap-3 mb-4 {{ !$hasReserves ? 'bg-green-50 p-2 border border-green-200' : 'opacity-40' }}">
                <div class="w-5 h-5 border-2 border-black flex-shrink-0 flex items-center justify-center font-bold">
                    {{ !$hasReserves ? 'X' : '' }}
                </div>
                <div>
                    <p class="font-bold uppercase text-xs">Constat sans réserve</p>
                    <p class="text-[10px]">L'Entreprise Générale reconnaît que les travaux sont terminés conformément aux règles de l'art.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 {{ $hasReserves ? 'bg-amber-50 p-2 border border-amber-200' : 'opacity-40' }}">
                <div class="w-5 h-5 border-2 border-black flex-shrink-0 flex items-center justify-center font-bold">
                    {{ $hasReserves ? 'X' : '' }}
                </div>
                <div class="w-full">
                    <p class="font-bold uppercase text-xs text-amber-800">Constat avec réserves</p>
                    <div class="min-h-[60px] border border-slate-200 mt-1 p-2 text-xs italic bg-white">
                        @if($hasReserves)
                            <ul>
                            @foreach($report->reserves as $reserve)
                                <li>{{ $reserve['description'] }}</li>
                            @endforeach
                            </ul>
                        @else
                            Aucune réserve signalée.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="border border-red-200 bg-red-50 p-3 mb-6">
        <h4 class="font-bold text-[10px] uppercase text-red-800 mb-1">Transfert des Risques et Garanties</h4>
        <p class="text-[10px] leading-snug italic">
            À compter de la signature de ce document, la garde de l'ouvrage est officiellement transférée à l'Entreprise Générale / Client.
            <strong>C2ME décline toute responsabilité</strong> en cas de dégradations ultérieures par d'autres corps d'état (chocs d'engins, perçages non autorisés, dégradations de peinture ou bardage).
        </p>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 gap-10">
        <div class="border border-slate-300 p-4 h-32 relative bg-white">
            <span class="absolute top-2 left-2 text-[9px] font-bold uppercase text-slate-400">Pour C2ME</span>
            <!-- Espace signature -->
        </div>
        <div class="border border-black p-4 h-32 relative bg-white">
            <span class="absolute top-2 left-2 text-[9px] font-bold uppercase text-slate-800">Pour Anuri France (Client)</span>
            <!-- Espace signature -->
        </div>
    </div>
@endsection
