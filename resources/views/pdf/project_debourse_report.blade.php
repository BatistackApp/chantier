@extends('pdf.layout')

@section('content')
    <div class="bg-white border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-black uppercase text-indigo-900">Rapport Interne : Déboursé & Rentabilité</h1>
        </div>
        <div class="grid grid-cols-3 gap-6 text-xs border-t border-slate-100 pt-4 uppercase font-bold text-slate-500">
            <div>Projet : <span class="text-slate-900">{{ $project->title }}</span></div>
            <div>Ref : <span class="text-slate-900">{{ $project->reference }}</span></div>
            <div>Date : <span class="text-slate-900">{{ now()->format('d/m/Y') }}</span></div>
        </div>
    </div>

    <!-- Dashboard de Rentabilité -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Vendu H.T.</p>
            <p class="text-xl font-black">{{ \Illuminate\Support\Number::currency($metrics['quoted_amount'], 'EUR') }}</p>
        </div>
        <div class="bg-white p-4 border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Déboursé Réel</p>
            <p class="text-xl font-black text-indigo-600">{{ \Illuminate\Support\Number::currency($metrics['actual_debourse'], 'EUR') }}</p>
        </div>
        <div class="bg-white p-4 border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Marge Brute</p>
            <p class="text-xl font-black {{ $metrics['margin_cents'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ \Illuminate\Support\Number::currency($metrics['margin_cents'], 'EUR') }}
            </p>
        </div>
        <div class="p-4 border-2 {{ $metrics['margin_percentage'] > 20 ? 'bg-green-600 border-green-700 text-white' : 'bg-white border-indigo-600' }}">
            <p class="text-[10px] font-bold opacity-70 uppercase mb-1">Taux de Marge</p>
            <p class="text-2xl font-black">{{ $metrics['margin_percentage'] }} %</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <!-- Détail du déboursé -->
        <section class="bg-white border border-slate-200 p-4">
            <h3 class="font-bold uppercase text-xs mb-4 border-b border-slate-100 pb-2">Décomposition du Déboursé (H.T.)</h3>
            <table class="w-full text-xs">
                @foreach($breakdown as $type => $data)
                    <tr class="border-b border-slate-50">
                        <td class="py-2 text-slate-500 italic">{{ $data['label'] }}</td>
                        <td class="py-2 text-right font-mono font-bold">{{ \Illuminate\Support\Number::currency($data['total'], 'EUR') }}</td>
                    </tr>
                @endforeach
                <tr class="bg-indigo-50 font-black">
                    <td class="py-3 px-2">COÛT DE REVIENT TOTAL</td>
                    <td class="py-3 px-2 text-right font-mono uppercase">{{ \Illuminate\Support\Number::currency($metrics['actual_debourse'], 'EUR') }}</td>
                </tr>
            </table>
        </section>

        <!-- Analyse vs Étude initiale -->
        <section class="bg-white border border-slate-200 p-4">
            <h3 class="font-bold uppercase text-xs mb-4 border-b border-slate-100 pb-2">Écart vs Étude</h3>
            <div class="space-y-6">
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase italic">Coût prévisionnel Étude</p>
                        <p class="text-lg font-bold">{{ \Illuminate\Support\Number::currency($project->estimated_cost, 'EUR') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase italic">Écart de réalisation</p>
                        <p class="text-lg font-black {{ $metrics['variance_from_estimate'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $metrics['variance_from_estimate'] >= 0 ? '+' : '' }} {{ number_format($metrics['variance_from_estimate'], 2, ',', ' ') }} €
                        </p>
                    </div>
                </div>

                <!-- Indicateur visuel -->
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-bold uppercase mb-2">Performance du Chantier :</p>
                    @if($metrics['is_within_estimate'])
                        <div class="bg-green-100 border-l-4 border-green-500 p-3 text-green-800 text-[11px] leading-tight font-medium">
                            Objectif financier atteint. Le déboursé réel est inférieur ou égal au prévisionnel.
                        </div>
                    @else
                        <div class="bg-red-100 border-l-4 border-red-500 p-3 text-red-800 text-[11px] leading-tight font-medium">
                            Dépassement de budget constaté. Analyse des écarts (temps passés ou achats) nécessaire.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
    <div class="mt-8 text-[10px] text-slate-400 text-center italic">
        Ce document est une extraction analytique interne. Il ne peut être transmis à des tiers.
    </div>
@endsection
