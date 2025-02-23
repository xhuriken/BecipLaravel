@extends('layouts.app')

@section('content')
    <div class="container-page min-width">
        <div class="items-container">
            @if (auth()->user()->role == 'engineer')
                <a href='{{ route('usermanager') }}' class='btn-return'>Gérer les utilisateurs</a>
            @endif
            @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                <a href="{{ route('projects.generate', [500, date('Y')]) }}" class="btn-mask">
                    Générer 500 affaires cette année
                </a>
                <a href="{{ route('projects.generate', [500, date('Y')+1]) }}" class="btn-mask">
                    Générer 500 affaires l'année suivante
                </a>
                <!-- Bouton qui ouvre le modal d'ajout -->
                <button id="toggleButton" class="btn-mask">Ajouter une affaire manuellement</button>
            @endif
        </div>

        <h2>Liste des affaires</h2>
        <table id="project-table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Nom de l'affaire</th>
                    <th>Actions</th>
                    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                        <th data-label="Delete"><i class="fa-solid fa-trash"></i></th>
                        <th><input type="checkbox" id="select-all"></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->company_id ? $project->getCompanyName($project->company_id) : 'Aucune' }}</td>
                        <td>{{ $project->name }}</td>
                        <td>
                            <a href="{{ route('projects.project', $project) }}" class="btn-return">Voir</a>
                            @if(auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                                <span class="responsiveSpan">|</span>
                                <a href="#" class="btn-return edit-project"
                                   data-project-id="{{ $project->id }}"
                                   data-project-name="{{ $project->name }}"
                                   data-company-id="{{ $project->company_id }}"
                                   data-referent-id="{{ $project->referent_id }}"
                                   data-address="{{ $project->address }}"
                                   data-comment="{{ $project->comment }}"
                                   data-clients="{{ json_encode($project->clients->pluck('id')->toArray()) }}">
                                    Modifier
                                </a>
                            @endif
                        </td>
                        @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                            <td class="icon-cell">
                                <a href="#" class="delete-project-btn" data-delete-url="{{ route('projects.delete', $project) }}">
                                    <i class="fa-solid fa-trash delete-icon"></i>
                                </a>
                            </td>
                            <td>
                                <input type="checkbox" class="delete-checkbox" data-project-id="{{ $project->id }}">
                            </td>
                        @endif
                    </tr>
                @empty
                    <!-- TODO: trouver un moyen d'utiliser un ptn de colspan avec datatable-->
                    <tr class="no-data">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
            <button id="delete-selected" class="btn-filter" data-route="{{ route('projects.delete-selected') }}">
                Supprimer les affaires sélectionnées
            </button>
            <form action="{{ route('projects.delete-empty') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-filter">Supprimer les affaires vides</button>
            </form>
        @endif
    </div>

    <!-- Modal d'ajout d'une affaire -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Ajouter une affaire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="add-project-form">
                        @csrf
                        <div class="mb-3">
                            <label for="add-project-company" class="form-label">Entreprise</label>
                            <select class="form-select" name="company_id" id="add-project-company">
                                <option value="">- Aucune -</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add-project-engineer" class="form-label">Ingénieur référent</label>
                            <select class="form-select" name="engineer_id" id="add-project-engineer">
                                <option value="">- Aucun -</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nom de l'affaire</label>
                            <div class="d-flex align-items-center">
                                <span class="me-1">B</span>
                                <input type="text" id="add-project-year" maxlength="2" pattern="\d{2}" required placeholder="00" class="form-control me-1" style="max-width: 60px;">
                                <span class="me-1">.</span>
                                <input type="text" id="add-project-number" maxlength="3" pattern="\d{3}" required placeholder="000" class="form-control" style="max-width: 80px;">
                            </div>
                            <!-- Champ caché pour le nom complet -->
                            <input type="hidden" name="project_name" id="add-project-name">
                        </div>
                        <div class="mb-3">
                            <label for="add-project-clients" class="form-label">Clients ayant accès</label>
                            <select id="add-project-clients" name="clients[]" multiple="multiple" class="form-select">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }} - {{ \App\Models\User::getCompanyName($client->company_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="submit-add-project-btn">Ajouter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification de l'affaire -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Modifier l'affaire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-project-form">
                        @csrf
                        <input type="hidden" name="project_id" id="edit-project-id">

                        <!-- Nom de l'affaire -->
                        <div class="mb-3">
                            <label class="form-label">Nom de l'affaire</label>
                            <div class="d-flex align-items-center">
                                <span class="me-1">B</span>
                                <input type="text" id="edit-project-year" maxlength="2" pattern="\d{2}" required placeholder="00" class="form-control me-1" style="max-width: 60px;">
                                <span class="me-1">.</span>
                                <input type="text" id="edit-project-number" maxlength="3" pattern="\d{3}" required placeholder="000" class="form-control" style="max-width: 80px;">
                            </div>
                            <input type="hidden" name="project_name" id="edit-project-name">
                        </div>

                        <!-- Entreprise -->
                        <div class="mb-3">
                            <label for="edit-project-company" class="form-label">Entreprise</label>
                            <select class="form-select" name="company_id" id="edit-project-company">
                                <option value="">- Aucune -</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Référent -->
                        <div class="mb-3">
                            <label for="edit-project-referent" class="form-label">Référent</label>
                            <select class="form-select" name="referent_id" id="edit-project-referent">
                                <option value="">- Aucun -</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Adresse -->
                        <div class="mb-3">
                            <label for="edit-project-address" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="edit-project-address" name="address" placeholder="Adresse (facultatif)">
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="edit-project-comment" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="edit-project-comment" name="comment" rows="3" placeholder="Commentaire (facultatif)"></textarea>
                        </div>

                        <!-- Clients ayant accès -->
                        <div class="mb-3">
                            <label for="edit-project-clients" class="form-label">Clients ayant accès</label>
                            <select id="edit-project-clients" name="clients[]" multiple="multiple" class="form-select">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }} - {{ \App\Models\User::getCompanyName($client->company_id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="save-project-btn">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        window.storeProjectUrl = '{{ route("projects.store") }}';
        window.updateProjectUrl = '{{ route("projects.update") }}';
        window.csrf_token = '{{ csrf_token() }}';
    </script>
@endsection
