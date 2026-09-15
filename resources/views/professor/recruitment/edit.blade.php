<x-app-layout title="PV de recrutement">
    @php
        $committee = old('committee', $data['committee'] ?? []);
        $shortlist = old('shortlist', $data['shortlist'] ?? []);
        $interviews = old('interviews', $data['interviews'] ?? []);
    @endphp
    <div lang="fr">
        <a href="{{ route('professor.recruitment.index') }}" class="d-inline-block mb-3">&larr; Choisir un autre sujet</a>
        <div class="card mb-4"><div class="card-body">
            <h2 class="h5">Intitulé du sujet de doctorat</h2>
            <p>{{ $subject->title }}</p>
            <h2 class="h5">Résumé du sujet de doctorat</h2>
            <p style="white-space: pre-line">{{ $subject->description }}</p>
            <h2 class="h5">Directeur de thèse</h2>
            <p class="mb-0">{{ $subject->professor->name }} — {{ $subject->professor->email }}</p>
        </div></div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Veuillez corriger les informations suivantes :</strong>
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('professor.recruitment.update', $subject) }}" data-recruitment-form>
            @csrf
            <fieldset class="card mb-4"><div class="card-body">
                <legend class="h5">Co-Directeur de thèse (facultatif)</legend>
                <div class="row g-3">
                    @foreach (['name' => 'Nom complet', 'email' => 'Adresse e-mail', 'institution' => 'Établissement'] as $field => $label)
                        <div class="col-md-4">
                            <label class="form-label" for="co_director_{{ $field }}">{{ $label }}</label>
                            <input class="form-control" id="co_director_{{ $field }}" name="co_director_{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old('co_director_'.$field, $data['co_director_'.$field] ?? '') }}">
                        </div>
                    @endforeach
                </div>
            </div></fieldset>

            <section class="card mb-4"><div class="card-body">
                <h2 class="h5">1. Données personnelles de tous les candidats ayant postulé</h2>
                <div class="table-responsive"><table class="table">
                    <thead><tr><th>N°</th><th>Nom</th><th>Prénom</th><th>Adresse e-mail</th></tr></thead>
                    <tbody>@forelse ($applications as $application)
                        <tr><td>{{ $loop->iteration }}</td><td>{{ $application->candidate->profile?->last_name ?: $application->candidate->name }}</td><td>{{ $application->candidate->profile?->first_name }}</td><td>{{ $application->candidate->email }}</td></tr>
                    @empty
                        <tr><td colspan="4">Aucune candidature pour ce sujet. Un PV nécessite au moins un candidat.</td></tr>
                    @endforelse</tbody>
                </table></div>
            </div></section>

            <fieldset class="card mb-4"><div class="card-body">
                <legend class="h5">2. Pré-sélection des dossiers de candidature</legend>
                <p class="text-muted">Comité : 3 membres distincts, à choisir ci-dessous. Le directeur de thèse ({{ $subject->professor->name }}) et, le cas échéant, le co-directeur sont automatiquement ajoutés en tête du comité dans le PV — le jury comptera donc 4 ou 5 membres au total. Choisissez un professeur ou saisissez un membre externe. Ce comité sera également indiqué pour les entretiens et les signatures.</p>
                @for ($i = 0; $i < 3; $i++)
                    <fieldset class="border rounded p-3 mb-3" data-committee-row>
                        <legend class="float-none w-auto fs-6 px-2">Membre {{ $i + 1 }} (obligatoire)</legend>
                        <label class="form-label" for="committee_{{ $i }}">Professeur de la plateforme</label>
                        <select class="form-select mb-3" id="committee_{{ $i }}" name="committee[{{ $i }}][professor_id]" data-committee-professor>
                            <option value="">Saisie manuelle / aucun membre</option>
                            @foreach ($professors as $professor)
                                <option value="{{ $professor->id }}" @selected(($committee[$i]['professor_id'] ?? '') == $professor->id)>{{ $professor->name }} — {{ $professor->email }}</option>
                            @endforeach
                        </select>
                        <div class="row g-3">
                            @foreach (['name' => 'Nom complet', 'email' => 'Adresse e-mail', 'institution' => 'Établissement'] as $field => $label)
                                <div class="col-md-4">
                                    <label class="form-label" for="member_{{ $i }}_{{ $field }}">{{ $label }} (saisie manuelle)</label>
                                    <input class="form-control" data-committee-manual id="member_{{ $i }}_{{ $field }}" name="committee[{{ $i }}][{{ $field }}]" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ $committee[$i][$field] ?? '' }}">
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                @endfor
            </div></fieldset>

            @foreach (['shortlist' => '3. Résultats de la Pré-sélection', 'interviews' => '4. Entretien de recrutement — Résultats des entretiens'] as $field => $heading)
                @php $rows = $field === 'shortlist' ? $shortlist : $interviews; @endphp
                <fieldset class="card mb-4"><div class="card-body">
                    <legend class="h5">{{ $heading }}</legend>
                    <p class="text-muted">{{ $field === 'shortlist' ? 'Retenez 1 à 5 candidats ayant postulé.' : 'Retenez 1 à 3 candidats de la pré-sélection précédente.' }} Notes de 0 à 100. Le classement est calculé par note décroissante ; en cas d’égalité, l’ordre saisi est conservé.</p>
                    @for ($i = 0; $i < ($field === 'shortlist' ? 5 : 3); $i++)
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label" for="{{ $field }}_{{ $i }}">Candidat {{ $i + 1 }}</label>
                                <select class="form-select" id="{{ $field }}_{{ $i }}" name="{{ $field }}[{{ $i }}][application_id]" data-result-select="{{ $field }}">
                                    <option value="">Sélectionner un candidat</option>
                                    @foreach ($applications as $application)
                                        <option value="{{ $application->id }}" @selected(($rows[$i]['application_id'] ?? '') == $application->id)>{{ $application->fullName() }} — {{ $application->candidate->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="{{ $field }}_{{ $i }}_score">{{ $field === 'shortlist' ? 'Note du dossier' : 'Note d’entretien' }} / 100</label>
                                <input class="form-control" id="{{ $field }}_{{ $i }}_score" name="{{ $field }}[{{ $i }}][score]" type="number" min="0" max="100" step="0.01" value="{{ $rows[$i]['score'] ?? '' }}">
                            </div>
                        </div>
                    @endfor
                </div></fieldset>
            @endforeach

            <div class="mb-4">
                <label class="form-label" for="report_date">Date du PV (obligatoire)</label>
                <input class="form-control" id="report_date" name="report_date" type="date" required value="{{ old('report_date', $data['report_date'] ?? now()->format('Y-m-d')) }}">
            </div>
            <p class="text-muted">Les grilles d’évaluation, le type de bourse et les avis institutionnels du modèle Word sont conservés. Les signatures sont à compléter sur le document. Enregistrer ce PV ne modifie pas les décisions d’admission des candidatures.</p>
            <div class="d-flex flex-wrap gap-3 mb-5">
                <button class="btn btn-outline-primary" type="submit" name="action" value="save" @disabled($applications->isEmpty())>Enregistrer le PV</button>
                <button class="btn btn-primary" type="submit" name="action" value="download" @disabled($applications->isEmpty())>Enregistrer et télécharger Word</button>
            </div>
        </form>
    </div>
</x-app-layout>
