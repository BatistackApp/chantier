@extends('pdf.layout')

@section('content')
    <div class="border-2 border-black p-4 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold uppercase tracking-tight">Fiche de Préparation Fabrication</h1>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t border-black pt-4">
            <div>
                <p class="text-sm uppercase font-semibold text-slate-500">Dossier :</p>
                <p class="text-lg font-bold">{{ $project->title }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm uppercase font-semibold text-slate-500">Référence Projet :</p>
                <p class="text-lg font-bold">{{ $project->reference }}</p>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <div class="bg-slate-100 border-x-2 border-t-2 border-black p-2">
            <h2 class="text-lg font-bold uppercase italic">Section 1 : Fabrication</h2>
        </div>
        <table class="w-full border-2 border-black text-left text-sm">
            <thead>
            <tr class="bg-slate-50 border-b-2 border-black uppercase font-bold">
                <th class="px-3 py-2 border-r border-black">Type de fabrication</th>
                <th class="px-3 py-2 border-r border-black w-24">Dimension</th>
                <th class="px-3 py-2 border-r border-black w-20 text-center">Qté</th>
                <th class="px-3 py-2 border-r border-black w-24">Couleur</th>
                <th class="px-3 py-2 w-32 text-center">Temps Réal.</th>
            </tr>
            </thead>
            <tbody>
            @forelse($fabrications as $item)
                <tr class="border-b border-slate-300">
                    <td class="px-3 py-2 border-r border-black font-medium">{{ $item->label }}</td>
                    <td class="px-3 py-2 border-r border-black text-center">{{ $item->dimensions }}</td>
                    <td class="px-3 py-2 border-r border-black text-center font-bold">{{ $item->quantity }}</td>
                    <td class="px-3 py-2 border-r border-black text-center">
                            <span class="inline-block px-2 py-0.5 border border-slate-400 bg-slate-50 rounded text-xs">
                                RAL {{ $item->color_code }}
                            </span>
                    </td>
                    <td class="px-3 py-2 text-center text-slate-400 italic">..................</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-8 text-center text-slate-400 italic">Aucun élément de fabrication enregistré.</td>
                </tr>
            @endforelse

            <!-- Lignes vides pour ajout manuel si nécessaire (confort atelier) -->
            @for ($i = 0; $i < 3; $i++)
                <tr class="border-b border-slate-300 h-10">
                    <td class="border-r border-black"></td>
                    <td class="border-r border-black"></td>
                    <td class="border-r border-black"></td>
                    <td class="border-r border-black"></td>
                    <td></td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
    <div>
        <div class="bg-slate-100 border-x-2 border-t-2 border-black p-2">
            <h2 class="text-lg font-bold uppercase italic">Section 2 : Quincaillerie et Consommables</h2>
        </div>
        <table class="w-full border-2 border-black text-left text-sm">
            <thead>
            <tr class="bg-slate-50 border-b-2 border-black uppercase font-bold">
                <th class="px-3 py-2 border-r border-black w-1/4">Type</th>
                <th class="px-3 py-2 border-r border-black">Désignation</th>
                <th class="px-3 py-2 w-24 text-center">Quantité</th>
            </tr>
            </thead>
            <tbody>
            @forelse($quincaillerie as $item)
                <tr class="border-b border-slate-300">
                    <td class="px-3 py-2 border-r border-black font-semibold">{{ $item->type }}</td>
                    <td class="px-3 py-2 border-r border-black">{{ $item->label }}</td>
                    <td class="px-3 py-2 text-center font-bold text-lg">{{ $item->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-3 py-8 text-center text-slate-400 italic">Aucune quincaillerie spécifiée.</td>
                </tr>
            @endforelse

            @for ($i = 0; $i < 2; $i++)
                <tr class="border-b border-slate-300 h-10">
                    <td class="border-r border-black"></td>
                    <td class="border-r border-black"></td>
                    <td></td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
    <div class="mt-12 grid grid-cols-2 gap-10">
        <div class="border border-slate-300 p-4">
            <p class="text-xs font-bold uppercase text-slate-500 mb-8">Observations Atelier :</p>
            <div class="border-b border-slate-200 mb-4"></div>
            <div class="border-b border-slate-200 mb-4"></div>
        </div>
        <div class="border border-black p-4 bg-slate-50 text-center flex flex-col justify-between">
            <p class="text-xs font-bold uppercase mb-2">Validation Responsable Atelier</p>
            <p class="text-[10px] text-slate-400 italic mb-10">Date et Signature</p>
            <div class="text-slate-200">................................................</div>
        </div>
    </div>
@endsection
