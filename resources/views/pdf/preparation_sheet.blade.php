@extends('pdf.layout')

@section('content')
    <div class="border-2 border-black p-4 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-tighter">Fiche de Préparation Chantier</h1>
                <p class="text-slate-500 italic mt-1">Document de coordination et logistique</p>
            </div>
            <div class="text-right">
                <span class="bg-black text-white px-3 py-1 text-xs font-bold uppercase">Batistack ERP</span>
                <p class="mt-2 font-mono font-bold text-lg">{{ $project->reference }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-y-2 border-t border-black pt-4">
            <div class="flex">
                <span class="font-bold w-40 uppercase text-xs text-slate-500">Désignation :</span>
                <span class="font-semibold">{{ $project->title }}</span>
            </div>
            <div class="flex">
                <span class="font-bold w-40 uppercase text-xs text-slate-500">Client :</span>
                <span class="font-semibold">{{ $project->customer->name ?? 'N/A' }}</span>
            </div>
            <div class="flex col-span-2">
                <span class="font-bold w-40 uppercase text-xs text-slate-500">Adresse Chantier :</span>
                <span class="font-semibold">{{ $project->address ?? 'Non spécifiée' }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">

        <!-- Colonne Gauche : Administratif & Logistique -->
        <div class="space-y-6">

            <!-- Conformité Loi 1975 -->
            <section class="border border-black">
                <div class="bg-black text-white px-3 py-1 font-bold uppercase text-xs">Conformité Loi 1975</div>
                <div class="p-3 space-y-2">
                    <div class="flex items-center">
                        <div class="w-5 h-5 border border-black mr-2 flex items-center justify-center font-bold">
                            {{ ($prep->subcontractor_form_ok ?? false) ? 'X' : '' }}
                        </div>
                        <span>Formulaire acceptation sous-traitant niveau 2</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-5 h-5 border border-black mr-2 flex items-center justify-center font-bold">
                            {{ ($prep->subcontractor_contract_ok ?? false) ? 'X' : '' }}
                        </div>
                        <span>Contrat de Sous-Traitance signé</span>
                    </div>
                </div>
            </section>

            <!-- Logistique -->
            <section class="border border-black">
                <div class="bg-slate-200 px-3 py-1 font-bold uppercase text-xs border-b border-black">Logistique & État des lieux</div>
                <div class="p-3">
                    @php $logistics = $prep->logistics_status ?? []; @endphp
                    <div class="grid grid-cols-1 gap-2 mb-4">
                        <div class="flex items-center italic">
                            <div class="w-4 h-4 border border-slate-400 mr-2 flex items-center justify-center text-xs">
                                {{ in_array('terrassement', $logistics) ? 'X' : '' }}
                            </div>
                            <span>Terrassement terminé</span>
                        </div>
                        <div class="flex items-center italic">
                            <div class="w-4 h-4 border border-slate-400 mr-2 flex items-center justify-center text-xs">
                                {{ in_array('platine', $logistics) ? 'X' : '' }}
                            </div>
                            <span>Platines / Plots Béton conformes</span>
                        </div>
                        <div class="flex items-center italic">
                            <div class="w-4 h-4 border border-slate-400 mr-2 flex items-center justify-center text-xs">
                                {{ in_array('securise', $logistics) ? 'X' : '' }}
                            </div>
                            <span>Chargement / Déchargement sécurisé</span>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-500 uppercase font-bold mb-1">Observations :</div>
                    <div class="min-h-[60px] border-b border-dotted border-slate-300">
                        {{ $prep->observations ?? 'Aucune observation logistique.' }}
                    </div>
                </div>
            </section>

            <!-- Moyens de Levage -->
            <section class="border border-black">
                <div class="bg-slate-200 px-3 py-1 font-bold uppercase text-xs border-b border-black">Moyens de Levage</div>
                <div class="p-3 grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Type de moyen</span>
                        <span class="font-bold border-b border-slate-200 block">{{ $prep->lifting_means ?? 'Non défini' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Nombre d'engins</span>
                        <span class="font-bold border-b border-slate-200 block">{{ $prep->lifting_count ?? 0 }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] uppercase text-slate-400 block">Fournisseur</span>
                        <span class="font-bold border-b border-slate-200 block">{{ $prep->lifting_provider ?? 'À confirmer' }}</span>
                    </div>
                </div>
            </section>

        </div>

        <!-- Colonne Droite : Social, Sécurité & Délais -->
        <div class="space-y-6">

            <!-- Social et Sécurité -->
            <section class="border border-black">
                <div class="bg-red-700 text-white px-3 py-1 font-bold uppercase text-xs">Social et Sécurité</div>
                <div class="p-3 space-y-4">
                    <div class="flex items-start">
                        <div class="w-5 h-5 border border-black mr-2 flex-shrink-0 flex items-center justify-center font-bold">X</div>
                        <p class="text-xs leading-tight">Attestations de vigilance et assurances fournies et en règle (Urssaf / Décennale).</p>
                    </div>
                    <div class="bg-slate-50 p-2 border border-slate-200">
                        <div class="flex items-center mb-2">
                            <div class="w-4 h-4 border border-black mr-2 flex items-center justify-center text-[10px]">
                                {{ ($prep->safety_nets_required ?? false) ? 'X' : '' }}
                            </div>
                            <span class="font-bold">Pose de filets de sécurité requise</span>
                        </div>
                        <span class="text-[10px] uppercase text-slate-400 block pl-6">Prestataire filets :</span>
                        <span class="font-semibold block pl-6 border-b border-slate-200">{{ $prep->safety_nets_provider ?? 'S.O' }}</span>
                    </div>
                </div>
            </section>

            <!-- Gestion des délais -->
            <section class="border border-black">
                <div class="bg-slate-200 px-3 py-1 font-bold uppercase text-xs border-b border-black">Gestion des Délais & Événements</div>
                <div class="p-3 space-y-4">
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 block">Calendrier d'exécution (Semaine de début) :</span>
                        <span class="text-lg font-bold">Semaine {{ $project->planned_start_date ? $project->planned_start_date->format('W') : '--' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center opacity-50 italic">
                            <div class="w-4 h-4 border border-slate-400 mr-2"></div>
                            <span>Constat de démarrage</span>
                        </div>
                        <div class="flex items-center opacity-50 italic">
                            <div class="w-4 h-4 border border-slate-400 mr-2"></div>
                            <span>Constat de Fin</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Clause contractuelle -->
            <div class="bg-slate-50 border border-slate-200 p-3 italic text-[10px] text-slate-500 leading-tight">
                Édité suivant les termes et indications contractuelles entre le sous-traitant et l’entreprise cliente.
                Toute modification majeure des conditions de levage ou d'accès doit faire l'objet d'un avenant ou d'une notification écrite immédiate.
            </div>

        </div>
    </div>

    <div class="mt-10 border-t-2 border-black pt-6 grid grid-cols-2 gap-10">
        <div class="text-center">
            <p class="text-[10px] font-bold uppercase mb-16">Visa Conducteur de Travaux C2ME</p>
            <div class="border-t border-black w-3/4 mx-auto pt-1 text-[10px] italic">Date et Signature</div>
        </div>
        <div class="text-center">
            <p class="text-[10px] font-bold uppercase mb-16">Visa Responsable Chantier Client</p>
            <div class="border-t border-black w-3/4 mx-auto pt-1 text-[10px] italic">Date et Signature</div>
        </div>
    </div>

    <!-- Footer de bas de page -->
    <div class="fixed bottom-0 left-0 right-0 text-[9px] text-slate-400 text-center pb-2 px-10">
        <div class="border-t border-slate-200 pt-1">
            Ce document constitue un support d'aide à la préparation. La sécurité sur site reste soumise au PPSPS du chantier en vigueur.
        </div>
    </div>

@endsection
