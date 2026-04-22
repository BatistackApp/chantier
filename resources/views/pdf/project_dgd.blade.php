@extends('pdf.layout')

@section('content')
    <div class="flex justify-between items-start border-b-2 border-slate-800 pb-4 mb-8">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-800">D.G.D</h1>
            <p class="text-lg font-bold">Décompte Général Définitif</p>
        </div>
        <div class="text-right">
            <div class="bg-slate-800 text-white px-4 py-1 font-bold text-sm mb-1 uppercase">Finalisation Financière</div>
            <p class="font-mono text-sm uppercase">Réf Projet: {{ $project->reference }}</p>
        </div>
    </div>

    <!-- Infos Contractuelles -->
    <div class="grid grid-cols-2 gap-8 mb-10">
        <div>
            <h3 class="text-[10px] font-bold uppercase text-slate-400 mb-2">Maître d'Ouvrage / Client</h3>
            <div class="border-l-4 border-slate-200 pl-4 uppercase font-bold text-md">
                {{ $project->customer->name }}<br>
                <span class="text-xs font-normal normal-case italic">{{ $project->address }}</span>
            </div>
        </div>
        <div>
            <h3 class="text-[10px] font-bold uppercase text-slate-400 mb-2">Objet du Marché</h3>
            <div class="border-l-4 border-slate-200 pl-4 font-bold">
                {{ $project->title }}
            </div>
        </div>
    </div>

    <!-- Récapitulatif Financier -->
    <table class="w-full border-collapse mb-10">
        <thead>
        <tr class="bg-slate-100 text-[10px] font-bold uppercase border-y border-slate-300">
            <th class="py-3 px-4 text-left">Désignation des Postes</th>
            <th class="py-3 px-4 text-right">Montant H.T.</th>
        </tr>
        </thead>
        <tbody class="text-sm">
        <tr>
            <td class="py-4 px-4 border-b border-slate-100">Montant du Marché Initial</td>
            <td class="py-4 px-4 border-b border-slate-100 text-right font-mono">{{ \Illuminate\Support\Number::currency($project->quoted_amount, 'EUR') }}</td>
        </tr>
        <tr class="bg-slate-50 font-bold">
            <td class="py-4 px-4 border-b border-slate-300">TOTAL MARCHÉ RÉVISÉ</td>
            <td class="py-4 px-4 border-b border-slate-300 text-right font-mono text-lg">{{ \Illuminate\Support\Number::currency($project->quoted_amount, 'EUR') }}</td>
        </tr>
        <tr>
            <td class="py-4 px-4 border-b border-slate-100 italic text-slate-500 italic pl-8">Déduction Compte Prorata (3%)</td>
            <td class="py-4 px-4 border-b border-slate-100 text-right font-mono text-red-600 italic">- {{ \Illuminate\Support\Number::currency($project->quoted_amount*0.03, 'EUR') }}</td>
        </tr>
        </tbody>
        <tfoot>
        <tr class="bg-slate-800 text-white">
            <td class="py-4 px-4 font-bold text-lg uppercase">SOLDE NET À PAYER H.T.</td>
            <td class="py-4 px-4 text-right font-mono text-2xl font-black">
                {{ \Illuminate\Support\Number::currency($project->quoted_amount * 0.97, 'EUR') }}
            </td>
        </tr>
        </tfoot>
    </table>
    <div class="fixed bottom-6 left-6 right-6 text-[8px] text-slate-400 border-t border-slate-100 pt-2">
        Ce décompte général définitif est établi sous réserve des taxes en vigueur (TVA) et vaut solde de tout compte pour l'exécution du marché cité en référence.
    </div>
@endsection
